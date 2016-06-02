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
        $eventArgs = \Mockery::mock('\Doctrine\ORM\Event\LoadClassMetadataEventArgs');
        $eventArgs->shouldReceive('getClassMetadata')->once()->andReturn(\Mockery::self());
        $eventArgs->shouldReceive('getName')->once()->andReturn('\stdClass');
        $eventArgs->shouldReceive('mapField')->never();

        $listener = new Timestampable();

        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithValidClassMapFields()
    {
        $timestampableTrait = $this->getObjectForTrait('\Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\Traits\TimestampableTrait');

        $eventArgs = \Mockery::mock('\Doctrine\ORM\Event\LoadClassMetadataEventArgs');
        $eventArgs->shouldReceive('getClassMetadata')->once()->andReturn(\Mockery::self());
        $eventArgs->shouldReceive('getName')->once()->andReturn($timestampableTrait);
        $eventArgs->shouldReceive('hasField')->andReturn(false);
        $eventArgs->shouldReceive('mapField')->with(array('fieldName' => 'createdAt', 'type' => 'datetime'))->once();
        $eventArgs->shouldReceive('mapField')->with(array('fieldName' => 'updatedAt', 'type' => 'datetime'))->once();
        $eventArgs->shouldReceive('addLifecycleCallback')->with('updatedTimestamps', 'prePersist')->once();
        $eventArgs->shouldReceive('addLifecycleCallback')->with('updatedTimestamps', 'preUpdate')->once();

        $listener = new Timestampable();
        $listener->loadClassMetadata($eventArgs);
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
