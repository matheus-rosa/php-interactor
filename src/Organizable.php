<?php

namespace MatheusRosa\PhpInteractor;

use Exception;

trait Organizable
{
    /**
     * @throws Exception
     */
    public static function call($params = [])
    {
        $context = new Context($params);
        $instance = new self;

        try {
            $aroundOutput = $instance->around($context);
            if ($aroundOutput === false) {
                return $context;
            }

            $instance->before($context);
            $instance->executePipeline($context);
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

    private function executePipeline(Context $context)
    {
        $pipeline = $this->organize();

        foreach ($pipeline as $interactor) {
            if (!in_array(Interactable::class, class_uses($interactor), true)) {
                throw new Exception("Class ${interactor} must use the Interactable trait");
            }

            $interactor::call($context);
        }
    }

    /**
     * @return array
     */
    protected abstract function organize();
}
