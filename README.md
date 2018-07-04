# doctrine-assigned-identity

## Description

This package allows you manually assign IDs to a Doctrine entity, even when the entity uses the stategies AUTO, SEQUENCE, IDENTITY or UUID.

This package's main use-case is to explicitly set IDs of entities that are created for your unit tests. It is not advised to use this package in production.

## Installation

```
composer require --dev tseho/doctrine-assigned-identity
```

## Usage

Register the EventListener in Doctrine.

#### With Symfony:
```
# app/config/config_test.yml

services:
    tseho.doctrine_assigned_identity.listener:
        class: Tseho\DoctrineAssignedIdentity\EventListener\AssignedIdentityListener
        public: false
        tags:
            - { name: doctrine.event_listener, event: prePersist }

```

## How does it work?

`AssignedIdentityListener` will override the ID generator of an entity class if there is a newly persisted instance
with a manually assigned id. For all the other instances of the same class without id, the `ChainedGenerator` will
fallback on the correct id generator.
