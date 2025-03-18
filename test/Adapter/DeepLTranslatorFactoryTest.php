<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Adapter;

use Atoolo\Translator\Adapter\DeepLTranslatorFactory;
use DeepL\DeepLException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeepLTranslatorFactory::class)]
class DeepLTranslatorFactoryTest extends TestCase
{
    /**
     * @throws DeepLException
     */
    public function testCreate(): void
    {
        $factory = new DeepLTranslatorFactory('test');
        $translator = $factory->create();

        $this->assertNotNull($translator, 'unexpected translator');
    }
}
