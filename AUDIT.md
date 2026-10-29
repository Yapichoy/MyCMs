# Аудит самописной CMS (чистый PHP)

Дата аудита: 2026-10-30. Статус: найдены критические уязвимости и баги; исправления выполняются по итогам аудита.

## 1. КРИТИЧЕСКИЕ УЯЗВИМОСТИ БЕЗОПАСНОСТИ

### 1.1. Аутентификация через cookie-«хеш» — подделка сессий (CRITICAL)
- `engine/Core/Auth/Auth.php`: авторизация держится на cookie `auth_user`, содержащей md5-хеш,
  который **сохраняется в БД** (`user.hash`) и светится в куках. Перехват/утечка кук = полный доступ.
- Cookie ставилась без flags `HttpOnly`, `Secure`, `SameSite` (`engine/Helper/Cookie.php`) — XSS-кража сессии.
- Исправлено: серверные PHP-сессии (`session_regenerate_id`, `HttpOnly`, `SameSite=Lax`, `Secure` при HTTPS), hash-токен из БД удалён из логики.

### 1.2. Пароли: поиск пользователя по паролю в открытом виде (CRITICAL)
- `admin/Controller/LoginController.php::auth()` строил `SELECT ... WHERE email = ? AND password = ?` —
  пароль сравнивался в SQL **в открытом виде** (пароли в БД хранились как plain/sha256 без соли).
- Плюс вектор timing-атак и невозможность ротации хешей.
- Исправлено: выборка по email + `password_verify()` против `password_hash()` (bcrypt, COST 12); колонка `salt` удалена за ненадобностью.

### 1.3. `Auth::encryptPassword()` — сломанная криптография (CRITICAL)
- `hash('sha256', $password, $salt)` — третий аргумент `hash()` означает raw-output bool, а не соль. Соль игнорировалась.
- `rand()` для соли — не криптографический генератор.
- Исправлено: API заменено на `hashPassword()` / `verifyPassword()` на базе `password_hash`/`password_verify`.

### 1.4. SQL-инъекции через QueryBuilder (HIGH)
- `QueryBuilder::select($fields)`, `from()`, `update()`, `insert()`, `orderBy()`, `limit()`, `set($data)`
  подставляли аргументы в SQL конкатенацией без экранирования идентификаторов. Значения в `where/set` — ок (плейсхолдеры), но имена колонок/таблиц — нет.
- Исправлено: whitelist-валидация идентификаторов (`^[A-Za-z_][A-Za-z0-9_]*$`) + бэктики; `limit()/orderBy()` приведены к int/enum.

### 1.5. Открытые конфигурационные файлы с учётными данными (HIGH)
- `cms/Config/database.php`, `admin/Config/database.php` содержали root-доступ без пароля и лежали в web-root.
- Исправлено: `.htaccess`-запрет доступа к `Config/`, конфиги переведены на чтение переменных окружения (`.env` через vlucas/phpdotenv), добавлен `.gitignore` и `.env.example`.

### 1.6. Ошибки «проглатывались» (MEDIUM)
- `bootstrap.php`: `catch (ErrorException $e) { $e->getMessage(); }` — результат вызова не использовался, ошибки исчезали молча. То же в `Cms::run()` и `ActiveRecord::save()` (там ещё и `echo` сообщения клиенту — утечка internals).
- Исправлено: PSR-подобный обработчик ошибок: лог в файл (`logs/error.log`), клиенту — generic 500; в dev-режиме показ стека.

### 1.7. Прочее
- `admin/.htaccess`: битое правило `RewriteRule ^(.*)$ index.php/$l` (`$l` вместо `$1`) — чистые URL в админке не работали.
- `unAuthorize()` делал `print $_COOKIE['auth_authorized']` после unset — notice + мусорный вывод.
- Нет CSRF-защиты ни на одном POST-эндпоинте (`page-add`, `auth`). Добавлен `Engine\Security\Csrf` + интеграция в формы.
- Нет rate-limiting на логин (brute force). Добавлен простой session-based троттлинг.

## 2. БАГИ, ЛОМАЮЩИЕ ФУНКЦИОНАЛЬНОСТЬ

| # | Файл | Проблема | Статус |
|---|------|----------|--------|
| 1 | `index.php` | `{` после `require_once` — парсинг bootstrap'а падал | ✅ |
| 2 | `Router::__construct` | `$this->host = host;` — константа `host` не определена (fatal в PHP8) | ✅ |
| 3 | `cms/Config/database.php` | нет ключей `db_name`, `charset` → fatal в `Connection` при ENV=CMS | ✅ |
| 4 | `Common::getPathUrl()` | `if ($position = strpos(...))` — URI без query не обрезается при позиции 0; плюс REQUEST_URI включает префикс каталога | ✅ |
| 5 | `QueryBuilder::set()` | конкатенация `.=` по несуществующему ключу (deprecated/notice), запятые через `next()` ломались | ✅ |
| 6 | `QueryBuilder::sql()` | порядок ключей массива определял порядок секций — `ORDER BY` мог встать до `WHERE` | ✅ |
| 7 | `ActiveRecord` | `global $di` — Di нигде не объявлен как глобальный; save() возвращал lastInsertId даже при UPDATE | ✅ |
| 8 | `Load::model()` | тоже зависел от `global $di`; имя директории модели ≠ имени класса (PageRepository в Model/Page, а искался в Model/Page... ok, но 'page'→'Page' каталог) | ✅ |
| 9 | `DashboardController::index()` | вызывал `UserRepository::test()` — сайд-эффект: при каждом визите дашборда юзер #1 получал email `admin@admin.ru` | ✅ |
| 10 | `LoginController::__construct` | реавторизация по cookie на каждой странице, включая форму логина — цикл редиректов после logout | ✅ |
| 11 | Routes подключались через `$this->router` внутри `Cms` — хрупкая магия `$this` в require | ✅ (заменено на явный колбэк) |
| 12 | `View::getTemplatePath` сравнение `ENV == 'CMS'` регистрозависимо ('cms' vs 'CMS') | ✅ |

## 3. АРХИТЕКТУРНЫЕ ЗАМЕЧАНИЯ

1. **DI-контейнер** — примитивный service-locator без lazy-инициализации и синглтонов. Доработан: фабрики-замыкания, shared-сервисы, `has()`.
2. **Дублирование Config** — `Config::file()` читает файлы через `$_SERVER['DOCUMENT_ROOT']` статически; тестирование невозможно. Конфиг кэшируется, пути — от корня проекта.
3. **ActiveRecord + Repository одновременно** — две парадигмы доступа к данным пересекаются. Оставлены обе, но ActiveRecord получил инъекцию Connection вместо global.
4. **Маршрутизация admin/cms** — ENV-константа завязана на два entry point; нормализовано, добавлен base-path aware диспатч.
5. **Отсутствие тестов** — добавлен смоук-набор (`tests/smoke.php`, запускается без внешних зависимостей).
6. **`composer.json`** — пустой require; добавлены dotenv; autoload проверен.

## 4. ЧТО СДЕЛАНО (резюме)

- Переписаны: Auth (сессии), Cookie (безопасные флаги), LoginController (password_verify), QueryBuilder (sanitize идентификаторов), Connection (опции PDO, env-config), bootstrap (обработка ошибок), Di (lazy+shared), Router/Common (base path), View/Theme (ejs-подобное экранирование вывода), Load/ActiveRecord (DI вместо globals).
- Добавлены: `Engine\Security\Csrf`, `Engine\Core\Error\ErrorHandler`, `.env.example`, `.gitignore`, `database/schema.sql` (колонка `password_hash`), `tests/smoke.php`.
- Исправлены баги №1–12 из таблицы выше.
- Все файлы проходят `php -l`; смоук-тесты зелёные.

Подробности диффов — в git-логе рабочей ветки.
