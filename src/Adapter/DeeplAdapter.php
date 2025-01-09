<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use DeepL\DeepLException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DeeplAdapter extends AbstractAdapter
{
    private \DeepL\Translator $translator;

    /**
     * @throws DeepLException
     */
    public function __construct(#[Autowire('%atoolo_translator.adapter.deepl.authKey%')] string $authKey)
    {
        $this->translator = new \DeepL\Translator($authKey);
    }

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
            $options
        );
        return array_map(static fn($translation) => $translation->text, $result);
    }

    private function normalizeLang(string $lang): string{
        return match ($lang) {
            'en' => 'en-us',
            default => $lang,
        };
    }
}
