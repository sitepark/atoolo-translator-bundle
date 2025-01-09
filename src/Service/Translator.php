<?php

declare(strict_types=1);

namespace Atoolo\Translator\Service;

use Atoolo\Translator\Adapter\AbstractAdapter;
use Atoolo\Translator\Dto\TranslationParameter;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Translator
{
    private DateInterval $ttl;

    public function __construct(
        #[Autowire(service: 'atoolo_translator.translator.adapter')]
        private readonly AbstractAdapter $adapter,
        private readonly CacheInterface $translationCache,
        #[Autowire(service: 'atoolo_translator.textHasher')]
        private readonly TextHasher $textHasher,
        #[Autowire('%atoolo_translator.translator.ttl%')]
        string $ttl,
    ) {
        $this->ttl = new DateInterval($ttl);
    }

    /**
     * @param array<string> $text
     * @return array<string>
     * @throws InvalidArgumentException
     */
    public function translate(array $text, TranslationParameter $parameter): array
    {
        /** @var array<string,string> $hashMapping */
        $hashMapping = [];
        foreach ($text as $value) {
            $hash = $this->textHasher->hash($value, $parameter);
            $hashMapping[$hash] = $value;
        }

        /** @var array<string,string> $translated */
        $translated = [];
        foreach ($hashMapping as $hash => $value) {
            $translated[$hash] = $this->translationCache->get($hash, function (ItemInterface $item) {
                $item->expiresAfter(60);
                return null;
            });
        }

        /** @var array<string,string> $toTranslate */
        $toTranslate = [];
        foreach ($translated as $hash => $translation) {
            if ($translation === null) {
                $toTranslate[] = $hashMapping[$hash];
            }
        }

        if (empty($toTranslate)) {
            /** @var array<string> $values */
            $values = array_values($translated);
            return $values;
        }

        $adapterTranslated = $this->adapter->translate($toTranslate, $parameter);

        $position = 0;
        foreach ($translated as $hash => $translation) {
            if ($translation === null) {
                $translatedValue = $adapterTranslated[$position];
                $translated[$hash] = $translatedValue;
                $this->translationCache->delete($hash);
                $this->translationCache->get($hash, function (ItemInterface $item) use ($translatedValue): string {
                    $item->expiresAfter($this->ttl);
                    return $translatedValue;
                });
                $position++;
            }
        }

        /** @var array<string> $values */
        $values = array_values($translated);
        return $values;
    }
}
