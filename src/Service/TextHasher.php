<?php

declare(strict_types=1);

namespace Atoolo\Translator\Service;

use Atoolo\Translator\Dto\TranslationParameter;

class TextHasher
{
    public function hash(string $text, TranslationParameter $parameter): string
    {
        return $parameter->sourceLang . '->' . $parameter->targetLang . ':' . hash('sha256', $text);
    }
}
