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

        if (!in_array('Yobrx\\Doctrine\\SimpleTimestampableBundle\\Doctrine\\Traits\\TimestampableTrait', $this->class_uses_deep($metadata->getName()))) {
            return;
        }

        if (!$metadata->hasField('createdAt')) {
            $metadata->mapField(
                array(
                    'fieldName' => 'createdAt',
                    'type'      => 'datetime'
                )
            );
        }

        if (!$metadata->hasField('updatedAt')) {
            $metadata->mapField(
                array(
                    'fieldName' => 'updatedAt',
                    'type'      => 'datetime'
                )
            );
        }

        $metadata->addLifecycleCallback('updatedTimestamps', 'prePersist');
        $metadata->addLifecycleCallback('updatedTimestamps', 'preUpdate');
    }

    /**
     * @param      $class
     * @param bool $autoload
     * @return mixed
     */
    public function class_uses_deep($class, $autoload = true)
    {
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}