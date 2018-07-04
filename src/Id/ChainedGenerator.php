<?php

namespace Tseho\DoctrineAssignedIdentity\Id;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Id\AssignedGenerator;

final class ChainedGenerator extends AssignedGenerator
{
    /**
     * @var AbstractIdGenerator
     */
    private $generator;

    /**
     * @param AbstractIdGenerator $generator
     */
    public function __construct(AbstractIdGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        $class = $em->getClassMetadata(get_class($entity));

        if (self::isIdAssigned($em, $entity)) {
            // If the id has been manually assigned to the entity, forward to AssignedGenerator::generate()
            return parent::generate($em, $entity);
        } else {
            // If not, fallback on the decorated generator
            $idValue = [$class->getSingleIdentifierFieldName() => $this->generator->generate($em, $entity)];
            $class->setIdentifierValues($entity, $idValue);

            return $idValue;
        }
    }

    /**
     * Check if the entity already has assigned IDs
     *
     * @param EntityManager $em
     * @param $entity
     * @return bool
     */
    public static function isIdAssigned(EntityManager $em, $entity)
    {
        $class = $em->getClassMetadata(get_class($entity));
        $idFields = $class->getIdentifierFieldNames();

        foreach ($idFields as $idField) {
            $value = $class->getFieldValue($entity, $idField);

            if (!isset($value)) {
                return false;
            }
        }

        return true;
    }
}
