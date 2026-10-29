<?php

namespace Engine;

use Engine\Di\Di;

/**
 * Загрузчик моделей.
 * Исправления: убран `global $di` (Di нигде не объявлялся глобальной переменной —
 * рантайм падал); DI передаётся явно через конструктор провайдера.
 */
class Load
{
    const MASK_MODEL_ENTITY     = '\\%s\\Model\\%s\\%s';
    const MASK_MODEL_REPOSITORY = '\\%s\\Model\\%s\\%sRepository';

    /** @var Di */
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function model($modelName, $modelDir = false)
    {
        $modelName = ucfirst($modelName);
        $modelDir  = $modelDir ? ucfirst($modelDir) : $modelName;

        $entityClass     = sprintf(self::MASK_MODEL_ENTITY, ENV, $modelDir, $modelName);
        $repositoryClass = sprintf(self::MASK_MODEL_REPOSITORY, ENV, $modelDir, $modelName);

        if (!class_exists($repositoryClass)) {
            throw new \InvalidArgumentException(sprintf('Repository %s not found', $repositoryClass));
        }

        $model = new \stdClass();
        $model->entity     = $entityClass;
        $model->repository = new $repositoryClass($this->di);
        return $model;
    }
}
