<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Service;

use Atoolo\Translator\Adapter\AbstractAdapter;
use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use Atoolo\Translator\Service\TextHasher;
use Atoolo\Translator\Service\Translator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[CoversClass(Translator::class)]
class TranslatorTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testTranslate(): void
    {
        $adapter = $this->createMock(AbstractAdapter::class);
        $cache = $this->createMock(CacheInterface::class);
        $textHasher = $this->createMock(TextHasher::class);
        $textHasher->method('hash')->willReturnCallback(fn(string $text, TranslationParameter $parameter) => $text);
        $parameter = new TranslationParameter('en', 'de', Format::TEXT);

        $cacheValues = [
            $textHasher->hash('computer', $parameter) => 'Computer',
            $textHasher->hash('water', $parameter) => 'Wasser',
            $textHasher->hash('book', $parameter) => 'Buch',
        ];

        $cache->method('get')->willReturnCallback(function (string $key, callable $callback) use ($cacheValues) {
            if (isset($cacheValues[$key])) {
                return $cacheValues[$key];
            }
            $item = $this->createMock(ItemInterface::class);
            return $callback($item);
        });

        $adapter->method('translate')->willReturn([
            'Apfel',
            'Fahrrad',
            'Himmel',
            'Telefon',
        ]);

        $translator = new Translator($adapter, $cache, $textHasher, 'P1D');


        $translation = $translator->translate([
            'apple',
            'computer',
            'bicycle',
            'sky',
            'water',
            'book',
            'telephone',
        ], $parameter);

        $expected = ['Apfel', 'Computer', 'Fahrrad', 'Himmel', 'Wasser', 'Buch', 'Telefon'];

        $this->assertEquals($expected, $translation, 'Unexpected translation');
    }

    public function testAllCached(): void
    {
        $adapter = $this->createMock(AbstractAdapter::class);
        $cache = $this->createMock(CacheInterface::class);
        $textHasher = $this->createMock(TextHasher::class);
        $textHasher->method('hash')->willReturnCallback(fn(string $text, TranslationParameter $parameter) => $text);
        $parameter = new TranslationParameter('en', 'de', Format::TEXT);

        $cacheValues = [
            $textHasher->hash('computer', $parameter) => 'Computer',
            $textHasher->hash('water', $parameter) => 'Wasser',
            $textHasher->hash('book', $parameter) => 'Buch',
        ];

        $cache->method('get')->willReturnCallback(function (string $key, callable $callback) use ($cacheValues) {
            if (isset($cacheValues[$key])) {
                return $cacheValues[$key];
            }
            $item = $this->createMock(ItemInterface::class);
            return $callback($item);
        });

        $translator = new Translator($adapter, $cache, $textHasher, 'P1D');

        $translation = $translator->translate([
            'computer',
            'water',
            'book',
        ], $parameter);

        $expected = ['Computer', 'Wasser', 'Buch'];

        $this->assertEquals($expected, $translation, 'Unexpected translation');
        $adapter->expects($this->never())->method('translate');
    }
}
