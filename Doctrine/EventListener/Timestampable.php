<?php

namespace Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Class Timestampable
 */
class Timestampable implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if (!in_array('Yobrx\\Doctrine\\SimpleTimestampableBundle\\Doctrine\\Traits\\TimestampableTrait', class_uses($metadata->getName())))
        {
            return;
        }

        $metadata->mapField(array(
            'fieldName' => 'createdAt',
            'type' => 'datetime'
        ));

        $metadata->mapField(array(
            'fieldName' => 'updatedAt',
            'type' => 'datetime'
        ));

        $metadata->addLifecycleCallback('updatedTimestamps', 'prePersist');
        $metadata->addLifecycleCallback('updatedTimestamps', 'preUpdate');
    }
}