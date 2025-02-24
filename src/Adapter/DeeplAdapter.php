<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use DeepL\DeepLException;

/**
 * @codeCoverageIgnore
 */
class DeeplAdapter extends AbstractAdapter
{
    /**
     * @throws DeepLException
     */
    public function __construct(private readonly \DeepL\Translator $translator) {}

    /**
     * @param array<string> $text
     * @return array<string>
     * @throws DeepLException
     */
    public function translate(array $text, TranslationParameter $parameter): array
    {
        $options = [];
        if ($parameter->format === Format::HTML) {
            $options['tagHandling'] = 'html';
        }
        $result = $this->translator->translateText(
            $text,
            $this->normalizeLang($parameter->sourceLang),
            $this->normalizeLang($parameter->targetLang),
            $options,
        );
        return array_map(static fn($translation) => $translation->text, $result);
    }

    private function normalizeLang(string $lang): string
    {
        return match ($lang) {
            'en' => 'en-us',
            default => $lang,
        };
    }
}
