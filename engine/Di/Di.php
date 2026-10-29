<?php

namespace Engine\Di;

/**
 * Микроди-контейнер.
 * Доработан: поддержка фабрик-замыканий (lazy), shared-сервисов (синглтонов), has().
 * Совместимость со старым API set()/get() сохранена.
 */
class Di
{
    /** @var array<string, mixed|callable> определения сервисов */
    private $definitions = [];
    /** @var array<string, mixed> уже созданные shared-инстансы */
    private $resolved = [];
    /** @var array<string, bool> карта "создавать ли инстанс один раз" */
    private $shared = [];

    /**
     * Зарегистрировать сервис. Значение-замыкание выполняется лениво при первом get().
     */
    public function set($key, $value)
    {
        $this->definitions[$key] = $value;
        unset($this->resolved[$key]);
        return $this;
    }

    /**
     * Shared-сервис: создаётся один раз за запрос (синглтон).
     */
    public function share($key, callable $factory)
    {
        $this->definitions[$key] = $factory;
        $this->shared[$key] = true;
        return $this;
    }

    public function has($key)
    {
        return isset($this->definitions[$key]) || array_key_exists($key, $this->resolved);
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }
        if (!isset($this->definitions[$key])) {
            return null;
        }
        $definition = $this->definitions[$key];
        $instance = is_callable($definition) ? $definition($this) : $definition;

        if (!empty($this->shared[$key])) {
            $this->resolved[$key] = $instance;
        }
        return $instance;
    }
}
