<?php

declare(strict_types=1);

namespace Atoolo\Translator\Service;

use Atoolo\Translator\Adapter\AbstractAdapter;
use Atoolo\Translator\Dto\TranslationParameter;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class Translator
{
    public function __construct(
        private readonly AbstractAdapter $adapter,
        private readonly CacheInterface $translationCache,
        private readonly TextHasher $textHasher,
    )
    {
    }

    /**
     * @param array<string> $text
     * @return array<string>
     * @throws InvalidArgumentException
     */
    public function translate(array $text, TranslationParameter $parameter): array
    {
        $hashMapping = [];
        foreach ($text as $value) {
            $hash = $this->textHasher->hash($value, $parameter);
            $hashMapping[$hash] = $this->translationCache->get($hash, function () {
                return null;
            });
        }

        $toTranslate = [];
        foreach ($hashMapping as $hash => $translation) {
            if ($translation === null) {
                $toTranslate[$hash] = $text[$hash];
            }
        }

        if (empty($toTranslate)) {
            return array_values($hashMapping);
        }

        $adapterTranslated = $this->adapter->translate($toTranslate, $parameter);

        $position = 0;
        foreach ($hashMapping as $hash => $translation) {
            if ($translation === null) {
                $hashMapping[$hash] = $adapterTranslated[$position];
                $this->translationCache->get($hash, fn() => $hashMapping[$hash]);
                $position++;
            }
        }

        return array_values($hashMapping);
    }
}
