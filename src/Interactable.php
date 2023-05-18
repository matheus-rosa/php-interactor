<?php

namespace MatheusRosa\PhpInteractor;

trait Interactable
{
    use Executor;

    protected function perform(Context $context)
    {
        return $this->execute($context);
    }

    /**
     * @param Context $context
     * @return Context
     */
    protected abstract function execute(Context $context);
}
