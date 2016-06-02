<?php

namespace Yobrx\Doctrine\SimpleTimestampableBundle\Tests\Doctrine\EventListener;

use Doctrine\ORM\Events;
use Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\EventListener\Timestampable;

class TimestampableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritDoc}
     */
    public function testGetSubscribedEvents()
    {
        $listener = new Timestampable();

        $this->assertEquals(array(Events::loadClassMetadata), $listener->getSubscribedEvents());
    }

    public function testLoadClassMetadataSkipClassNotUseTrait()
    {
        $metadata = $this->prophesize('\Doctrine\ORM\Mapping\ClassMetadataInfo');
        $metadata->getName()->willReturn('\stdClass');
        $metadata->addLifecycleCallback()->shouldNotBeCalled();

        $eventArgs = $this->prophesize('\Doctrine\ORM\Event\LoadClassMetadataEventArgs');
        $eventArgs->getClassMetadata()->willReturn($metadata->reveal())->shouldBeCalled();

        $listener = new Timestampable();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithValidClassMapFields()
    {
        $timestampableTrait = $this->getObjectForTrait('\Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\Traits\TimestampableTrait');
        $metadata           = $this->prophesize('\Doctrine\ORM\Mapping\ClassMetadataInfo');
        $metadata->getName()->willReturn($timestampableTrait);
        $metadata->hasField('createdAt')->willReturn(false)->shouldBeCalled();
        $metadata->mapField(array('fieldName' => 'createdAt', 'type' => 'datetime'))->shouldBeCalled();
        $metadata->hasField('updatedAt')->willReturn(false)->shouldBeCalled();
        $metadata->mapField(array('fieldName' => 'updatedAt', 'type' => 'datetime'))->shouldBeCalled();
        $metadata->addLifecycleCallback("updatedTimestamps", "prePersist")->shouldBeCalled();
        $metadata->addLifecycleCallback("updatedTimestamps", "preUpdate")->shouldBeCalled();

        $eventArgs = $this->prophesize('\Doctrine\ORM\Event\LoadClassMetadataEventArgs');
        $eventArgs->getClassMetadata()->willReturn($metadata->reveal())->shouldBeCalled();

        $listener = new Timestampable();
        $listener->loadClassMetadata($eventArgs->reveal());
    }
}
