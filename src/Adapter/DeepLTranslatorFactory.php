<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use DeepL\DeepLException;
use DeepL\Translator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DeepLTranslatorFactory
{
    public function __construct(#[Autowire('%atoolo_translator.adapter.deepl.authKey%')] private readonly string $authKey) {}

    /**
     * @throws DeepLException
     */
    public function create(): DeeplAdapter
    {
        return new DeeplAdapter(new Translator($this->authKey));
    }
}
