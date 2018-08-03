<?php

namespace Tseho\DoctrineAssignedIdentity;

trait WritableIdTrait
{
    /**
     * @param $id
     * @return $this
     * @throws \LogicException When trying to overwrite an existing id.
     */
    public function setId($id)
    {
        if ($this->id) {
            throw new \LogicException('You are not allowed to overwrite an existing id.');
        }

        $this->id = $id;

        return $this;
    }
}
