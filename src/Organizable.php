<?php

namespace MatheusRosa\PhpInteractor;

use Exception;

trait Organizable
{
    use Executor;

    protected function perform(Context $context)
    {
        $pipeline = $this->organize();

        foreach ($pipeline as $interactor) {
            if (!\in_array(Interactable::class, class_uses($interactor), true)) {
                throw new Exception("Class {$interactor} must use the Interactable trait");
            }

            $interactor::call($context);
        }
    }

    /**
     * @return array
     */
    abstract protected function organize();
}
