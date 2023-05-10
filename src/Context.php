<?php

namespace MatheusRosa\PhpInteractor;

class Context
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var bool
     */
    public $strictMode = false;

    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function success()
    {
        return empty($this->errors);
    }

    public function failure()
    {
        return !empty($this->errors);
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @throws ContextFailureException
     */
    public function fail($message, $strict = false)
    {
        $this->errors[] = $message;
        $this->strictMode = $strict;

        throw new ContextFailureException($message);
    }

    public function __get($name)
    {
        return $this->params[$name] ?: null;
    }

    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

    private function setParams($params)
    {
        if (!empty($params)) {
            $this->params = $params;
        }
    }
}
