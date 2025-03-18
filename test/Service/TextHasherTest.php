<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Service;

use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use Atoolo\Translator\Service\TextHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextHasher::class)]
class TextHasherTest extends TestCase
{
    public function testHash(): void
    {
        $text = 'Hello, World!';
        $parameter = new TranslationParameter('en', 'de', Format::TEXT);
        $hasher = new TextHasher();
        $hash = $hasher->hash($text, $parameter);
        $this->assertSame('en->de=dffd6021bb2bd5b0af676290809ec3a53191dd81c7f70a4b28688a362182986f', $hash);
    }
}
