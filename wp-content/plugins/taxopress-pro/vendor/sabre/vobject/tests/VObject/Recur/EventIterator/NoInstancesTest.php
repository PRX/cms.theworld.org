<?php

namespace Sabre\VObject\Recur\EventIterator;

use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Reader;
use Sabre\VObject\Recur\EventIterator;
use Sabre\VObject\Recur\NoInstancesException;

class NoInstancesTest extends TestCase
{
    public function testRecurrence()
    {
        $this->expectException(NoInstancesException::class);
        $input = <<<ICS
BEGIN:VCALENDAR
PRODID:-//Google Inc//Google Calendar 70.9054//EN
VERSION:2.0
BEGIN:VEVENT
DTSTART;TZID=Europe/Berlin:20130329T140000
DTEND;TZID=Europe/Berlin:20130329T153000
RRULE:FREQ=WEEKLY;BYDAY=FR;UNTIL=20130412T115959Z
EXDATE;TZID=Europe/Berlin:20130405T140000
EXDATE;TZID=Europe/Berlin:20130329T140000
DTSTAMP:20140916T201215Z
UID:foo
SEQUENCE:1
SUMMARY:foo
END:VEVENT
END:VCALENDAR
ICS;

        $vcal = Reader::read($input);
        $this->assertInstanceOf(VCalendar::class, $vcal);

        $it = new EventIterator($vcal, 'foo');
    }
}
