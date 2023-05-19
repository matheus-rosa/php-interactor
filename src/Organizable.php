<?php

namespace MatheusRosa\PhpInteractor;

use Exception;

trait Organizable
{
    use Executor;

    /**
     * @return false
     */
    protected function continueOnFailure()
    {
        return false;
    }

    protected function perform(Context $context)
    {
        $pipeline = $this->organize();
        $executedInteractors = [];
        $pipelineFailed = false;

        foreach ($pipeline as $interactor) {
            if (!\in_array(Interactable::class, class_uses($interactor), true)) {
                throw new Exception("Class {$interactor} must use the Interactable trait");
            }

            $interactor::call($context);
            $executedInteractors[] = $interactor;

            if ($context->failure()) {
                $pipelineFailed = true;

                if (!$this->continueOnFailure()) {
                    break;
                }
            }
        }

        if ($pipelineFailed && !empty($executedInteractors) && !$this->continueOnFailure()) {
            $this->rollbackExecutedInteractors(
                array_reverse($executedInteractors),
                $context
            );
        }
    }

    /**
     * @return array
     */
    abstract protected function organize();

    /**
     * @param Interactable[] $executedInteractors
     * @param Context        $context
     *
     * @return void
     */
    private function rollbackExecutedInteractors(array $executedInteractors, Context $context)
    {
        foreach ($executedInteractors as $interactor) {
            $instance = new $interactor();
            $instance->rollback($context);
        }
    }
}
