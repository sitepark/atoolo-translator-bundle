<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use Atoolo\Translator\Dto\TranslationParameter;

abstract class AbstractAdapter
{
    /**
     * @param array<string> $text
     * @return array<string>
     */
    abstract public function translate(array $text, TranslationParameter $parameter): array;
}