<?php

declare(strict_types=1);

namespace Sabre\Xml\Serializer;

use PHPUnit\Framework\TestCase;
use Sabre\Xml\Service;

class EnumTest extends TestCase
{
    public function testSerialize(): void
    {
        $service = new Service();
        $service->namespaceMap['urn:test'] = null;

        $xml = $service->write('{urn:test}root', function ($writer) {
            enum($writer, [
                '{urn:test}foo1',
                '{urn:test}foo2',
            ]);
        });

        $expected = <<<XML
<?xml version="1.0"?>
<root xmlns="urn:test">
   <foo1/>
   <foo2/>
</root>
XML;

        self::assertXmlStringEqualsXmlString($expected, $xml);
    }
}
