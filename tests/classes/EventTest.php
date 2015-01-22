<?php

use Foolz\Plugin\Event as Event;

class EventTest extends PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $this->assertEmpty(Event::getByKey('testing'));

        $new = new Event('testing');
        $retrieved = Event::getByKey('testing');
        $this->assertSame($new, $retrieved[0]);
    }

    public function testSetGetPriority()
    {
        $new = Event::forge('testing');
        $this->assertSame(5, $new->getPriority());
        $new->setPriority(8);
        $this->assertSame(8, $new->getPriority());
    }

    public function testSetGetCall()
    {
        $new = new Event('testing');
        $this->assertNull($new->getCall());

        $new->setCall(function(){ return 123; });
        $call = $new->getCall();
        $this->assertSame(123, $call());
    }
}
