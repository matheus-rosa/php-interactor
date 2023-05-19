<?php

namespace MatheusRosa\PhpInteractor;

use Exception;

trait Executor
{
    /**
     * @param $params
     * @return Context
     * @throws Exception
     */
    public static function call($params = [])
    {
        $context = $params instanceof Context ? $params : new Context($params);
        $instance = new self();

        try {
            $aroundOutput = $instance->around($context);
            if ($aroundOutput === false) {
                return $context;
            }

            $instance->before($context);
            $instance->perform($context);
            $instance->after($context);
        } catch (Exception $e) {
            if (!$e instanceof ContextFailureException || $context->strictMode) {
                throw $e;
            }
        }

        return $context;
    }

    abstract protected function perform(Context $context);

    /**
     * @param Context $context
     * @return null
     */
    protected function around(Context $context)
    {
        return null;
    }

    /**
     * @param Context $context
     * @return void
     */
    protected function before(Context $context)
    {
        // the default behavior is to do nothing, but it can be easily extended
        // in the final implementation class
    }

    /**
     * @param Context $context
     * @return void
     */
    protected function after(Context $context)
    {
        // the default behavior is to do nothing, but it can be easily extended
        // in the final implementation class
    }
}
