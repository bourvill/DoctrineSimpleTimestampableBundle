<?php

namespace Yobrx\Doctrine\SimpleTimestampableBundle\Tests\Doctrine\Traits;

use Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\Traits\TimestampableTrait;

class TimestampableTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdatedTimestampsWithCreatedAtIsNull()
    {
        /**
         * @var TimestampableTrait $timestampableTrait
         */
        $timestampableTrait = $this->getObjectForTrait('\Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\Traits\TimestampableTrait');
        $this->assertEquals(null, $timestampableTrait->getCreatedAt());
        $this->assertEquals(null, $timestampableTrait->getUpdatedAt());
        $timestampableTrait->updatedTimestamps();
        $this->assertInstanceOf('\DateTime', $timestampableTrait->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $timestampableTrait->getUpdatedAt());
    }

    public function testUpdatedTimestampsWithCreatedAtNotNullThenNoChangeOnCreatedAt()
    {
        $date = new \DateTime();
        /**
         * @var TimestampableTrait $timestampableTrait
         */
        $timestampableTrait = $this->getObjectForTrait('\Yobrx\Doctrine\SimpleTimestampableBundle\Doctrine\Traits\TimestampableTrait');
        $timestampableTrait->setCreatedAt($date);
        $this->assertEquals($date, $timestampableTrait->getCreatedAt());
        $this->assertEquals(null, $timestampableTrait->getUpdatedAt());
        $timestampableTrait->updatedTimestamps();

        $this->assertInstanceOf('\DateTime', $timestampableTrait->getCreatedAt());
        $this->assertSame($date, $timestampableTrait->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $timestampableTrait->getUpdatedAt());
    }
}