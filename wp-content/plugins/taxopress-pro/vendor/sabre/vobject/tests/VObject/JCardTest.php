<?php

namespace Sabre\VObject;

use PHPUnit\Framework\TestCase;

class JCardTest extends TestCase
{
    public function testToJCard()
    {
        $card = new Component\VCard([
            'VERSION' => '4.0',
            'UID' => 'foo',
            'BDAY' => '19850407',
            'REV' => '19951031T222710Z',
            'LANG' => 'nl',
            'N' => ['Last', 'First', 'Middle', '', ''],
            'item1.TEL' => '+1 555 123456',
            'item1.X-AB-LABEL' => 'Walkie Talkie',
            'ADR' => [
                '',
                '',
                ['My Street', 'Left Side', 'Second Shack'],
                'Hometown',
                'PA',
                '18252',
                'U.S.A',
            ],
        ]);

        $card->add('BDAY', '1979-12-25', ['VALUE' => 'DATE', 'X-PARAM' => [1, 2]]);
        $card->add('BDAY', '1979-12-25T02:00:00', ['VALUE' => 'DATE-TIME']);

        $card->add('X-TRUNCATED', '--1225', ['VALUE' => 'DATE']);
        $card->add('X-TIME-LOCAL', '123000', ['VALUE' => 'TIME']);
        $card->add('X-TIME-UTC', '12:30:00Z', ['VALUE' => 'TIME']);
        $card->add('X-TIME-OFFSET', '12:30:00-08:00', ['VALUE' => 'TIME']);
        $card->add('X-TIME-REDUCED', '23', ['VALUE' => 'TIME']);
        $card->add('X-TIME-TRUNCATED', '--30', ['VALUE' => 'TIME']);

        $card->add('X-KARMA-POINTS', '42', ['VALUE' => 'INTEGER']);
        $card->add('X-GRADE', '1.3', ['VALUE' => 'FLOAT']);

        $card->add('TZ', '-0500', ['VALUE' => 'UTC-OFFSET']);

        $expected = [
            'vcard',
            [
                [
                    'version',
                    new \stdClass(),
                    'text',
                    '4.0',
                ],
                [
                    'prodid',
                    new \stdClass(),
                    'text',
                    '-//Sabre//Sabre VObject '.Version::VERSION.'//EN',
                ],
                [
                    'uid',
                    new \stdClass(),
                    'text',
                    'foo',
                ],
                [
                    'bday',
                    new \stdClass(),
                    'date-and-or-time',
                    '1985-04-07',
                ],
                [
                    'bday',
                    (object) [
                        'x-param' => [1, 2],
                    ],
                    'date',
                    '1979-12-25',
                ],
                [
                    'bday',
                    new \stdClass(),
                    'date-time',
                    '1979-12-25T02:00:00',
                ],
                [
                    'rev',
                    new \stdClass(),
                    'timestamp',
                    '1995-10-31T22:27:10Z',
                ],
                [
                    'lang',
                    new \stdClass(),
                    'language-tag',
                    'nl',
                ],
                [
                    'n',
                    new \stdClass(),
                    'text',
                    ['Last', 'First', 'Middle', '', ''],
                ],
                [
                    'tel',
                    (object) [
                        'group' => 'item1',
                    ],
                    'text',
                    '+1 555 123456',
                ],
                [
                    'x-ab-label',
                    (object) [
                        'group' => 'item1',
                    ],
                    'unknown',
                    'Walkie Talkie',
                ],
                [
                    'adr',
                    new \stdClass(),
                    'text',
                        [
                            '',
                            '',
                            ['My Street', 'Left Side', 'Second Shack'],
                            'Hometown',
                            'PA',
                            '18252',
                            'U.S.A',
                        ],
                ],
                [
                    'x-truncated',
                    new \stdClass(),
                    'date',
                    '--12-25',
                ],
                [
                    'x-time-local',
                    new \stdClass(),
                    'time',
                    '12:30:00',
                ],
                [
                    'x-time-utc',
                    new \stdClass(),
                    'time',
                    '12:30:00Z',
                ],
                [
                    'x-time-offset',
                    new \stdClass(),
                    'time',
                    '12:30:00-08:00',
                ],
                [
                    'x-time-reduced',
                    new \stdClass(),
                    'time',
                    '23',
                ],
                [
                    'x-time-truncated',
                    new \stdClass(),
                    'time',
                    '--30',
                ],
                [
                    'x-karma-points',
                    new \stdClass(),
                    'integer',
                    42,
                ],
                [
                    'x-grade',
                    new \stdClass(),
                    'float',
                    1.3,
                ],
                [
                    'tz',
                    new \stdClass(),
                    'utc-offset',
                    '-05:00',
                ],
            ],
        ];

        $this->assertEquals($expected, $card->jsonSerialize());
    }
}
