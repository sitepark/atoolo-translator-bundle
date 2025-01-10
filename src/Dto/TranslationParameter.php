<?php

declare(strict_types=1);

namespace Atoolo\Translator\Dto;

/**
 * @codeCoverageIgnore
 */
class TranslationParameter
{
    public function __construct(
        public string $sourceLang,
        public string $targetLang,
        public Format $format,
    ) {}
}
