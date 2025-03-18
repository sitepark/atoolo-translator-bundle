<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Adapter;

use Atoolo\Translator\Adapter\DeeplAdapter;
use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use DeepL\DeepLException;
use DeepL\TextResult;
use DeepL\Translator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeeplAdapter::class)]
class DeeplAdapterTest extends TestCase
{
    /**
     * @throws DeepLException
     * @throws Exception
     */
    public function testTranslate(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->method('translateText')
            ->with(
                ['test'],
                'en-us',
                'de',
                [
                    'tagHandling' => 'html',
                ],
            )
            ->willReturn([new TextResult('test', 'en', 0)]);
        $adapter = new DeeplAdapter($translator);
        $parameter = new TranslationParameter('en', 'de', Format::HTML);
        $result = $adapter->translate(['test'], $parameter);

        $expected = ['test'];

        $this->assertEquals($expected, $result, 'unexpected result');
    }
}
