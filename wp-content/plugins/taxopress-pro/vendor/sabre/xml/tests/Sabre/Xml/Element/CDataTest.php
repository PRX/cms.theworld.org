<?php

declare(strict_types=1);

namespace Sabre\Xml\Element;

use PHPUnit\Framework\TestCase;
use Sabre\Xml\Reader;
use Sabre\Xml\Writer;

class CDataTest extends TestCase
{
    public function testDeserialize(): void
    {
        $this->expectException(\LogicException::class);
        $input = <<<BLA
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns">
 <blabla />
</root>
BLA;

        $reader = new Reader();
        $reader->elementMap = [
            '{http://sabredav.org/ns}blabla' => 'Sabre\\Xml\\Element\\Cdata',
        ];
        $reader->xml($input);

        $output = $reader->parse();
    }

    public function testSerialize(): void
    {
        $writer = new Writer();
        $writer->namespaceMap = [
            'http://sabredav.org/ns' => null,
        ];
        $writer->openMemory();
        $writer->startDocument('1.0');
        $writer->setIndent(true);
        $writer->write([
            '{http://sabredav.org/ns}root' => new Cdata('<foo&bar>'),
        ]);

        $output = $writer->outputMemory();

        $expected = <<<XML
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns"><![CDATA[<foo&bar>]]></root>

XML;

        self::assertEquals($expected, $output);
    }
}
