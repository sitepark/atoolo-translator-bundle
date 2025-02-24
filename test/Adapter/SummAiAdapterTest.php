<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Adapter;

use Atoolo\Translator\Adapter\SummAiAdapter;
use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SummAiAdapter::class)]
class SummAiAdapterTest extends TestCase
{
    /**
     * Can be used to run temporary tests against the real interface.
     */
    public function testTranslate(): void
    {

        $this->markTestSkipped( 'Test sleeve for temporary real tests against the real interface.' );

        $user = 'email';
        $apiKey = '123;';
        $adapter = new SummAiAdapter($user, $apiKey);
        $params = new TranslationParameter('de', 'de', Format::HTML);
        $translated = $adapter->translate([
            'Ein kleiner Text',
            'Ein zweiter Text',
            'Ein dritter Text',
            'Ein vierter Text',
            'Ein fünfter Text',
            'Ein sechster Text',
            'Ein siebter Text',
            'Ein achter Text',
            'Ein neunter Text',
        ], $params);

        print_r($translated);
    }
}
