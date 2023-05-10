<?php

namespace MatheusRosa\PhpInteractor;

use Exception;

trait Interactable
{
    /**
     * @param null|array|Context $contextOrParams
     * @return Context
     * @throws Exception
     */
    public static function call($contextOrParams = null)
    {
        $context = $contextOrParams instanceof Context ? $contextOrParams : new Context($contextOrParams);
        $instance = new self;

        try {
            $aroundOutput = $instance->around($context);
            if ($aroundOutput === false) {
                return $context;
            }

            $instance->before($context);
            $instance->execute($context);
            $instance->after($context);
        } catch (Exception $e) {
            if (!$e instanceof ContextFailureException || $context->strictMode) {
                throw $e;
            }
        }

        return $context;
    }

    protected function around(Context $context)
    {
        return null;
    }

    protected function before(Context $context)
    {
    }

    protected function after(Context $context)
    {
    }

    protected abstract function execute(Context $context);
}
