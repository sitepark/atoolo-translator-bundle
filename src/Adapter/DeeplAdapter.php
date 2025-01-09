<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use DeepL\DeepLException;

class DeeplAdapter extends AbstractAdapter
{
    private \DeepL\Translator $translator;

    /**
     * @throws DeepLException
     */
    public function __construct(string $authKey)
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
        return $this->translator->translateText($text, $parameter->sourceLang, $parameter->targetLang, $options);
    }
}
