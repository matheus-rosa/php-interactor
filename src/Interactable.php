<?php

namespace MatheusRosa\PhpInteractor;

trait Interactable
{
    use Executor;

    public function rollback(Context $context)
    {
        // In case an Interactor had ran already through
        // an Organizer pipeline that happens to have failed,
        // each Interactor has a chance to revert the applied
        // changes on it.
    }

    protected function perform(Context $context)
    {
        return $this->execute($context);
    }

    /**
     * @param Context $context
     *
     * @return Context
     */
    abstract protected function execute(Context $context);
}
