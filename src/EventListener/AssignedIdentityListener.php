<?php

namespace Tseho\DoctrineAssignedIdentity\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tseho\DoctrineAssignedIdentity\Id\ChainedGenerator;

class AssignedIdentityListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var EntityManager $em */
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        // Skip when the id has not been manually assigned before persist.
        // We are only replacing the idGenerator when there is at least one instance
        // of the entity with an assigned id.
        if (!ChainedGenerator::isIdAssigned($em, $entity)) {
            return;
        }

        $metadata = $em->getClassMetadata(get_class($entity));

        // Skip when the generator is of type AssignedGenerator.
        // ChainedGenerator will also be skipped because of the class inheritance.
        if ($metadata->idGenerator instanceof AssignedGenerator) {
            return;
        }

        // Replace the current generator with a ChainedGenerator
        $metadata->generatorType = ClassMetadata::GENERATOR_TYPE_CUSTOM;
        $metadata->idGenerator = new ChainedGenerator($metadata->idGenerator);
    }
}
