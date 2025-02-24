<?php

declare(strict_types=1);

namespace Atoolo\Translator\Adapter;

use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use CurlHandle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @codeCoverageIgnore
 */
class SummAiAdapter extends AbstractAdapter
{
    public function __construct(
        #[Autowire('%atoolo_translator.adapter.summai.user%')] private readonly string $user,
        #[Autowire('%atoolo_translator.adapter.summai.authKey%')] private readonly string $authKey,
    ) {}

    /**
     * @throws \JsonException
     */
    public function translate(array $text, TranslationParameter $parameter): array
    {
        $multiCurl = curl_multi_init();
        $handles = [];
        foreach ($text as $t) {
            $curl = $this->createCurlHandler($t, $parameter);
            $handles[] = $curl;
            curl_multi_add_handle($multiCurl, $curl);
        }

        $translated = [];
        $stillRunning=0;
        do {
            curl_multi_exec($multiCurl, $stillRunning);
        } while($stillRunning > 0);
        foreach($handles as $curl) {
            $content = curl_multi_getcontent($curl);
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $translated[] = $data['translated_text'];
            curl_multi_remove_handle($multiCurl, $curl);
            curl_close($curl);
        }
        curl_multi_close($multiCurl);

        return $translated;
    }

    /**
     * @throws \JsonException
     */
    private function createCurlHandler(string $text, TranslationParameter $parameter): CurlHandle
    {
        $inputTextType = $parameter->format === Format::HTML ? 'html' : 'plain_text';
        $url = "https://backend.summ-ai.com/api/v1/translation/";
        $data = [
            "user" => $this->user,
            "input_text" => $text,
            "input_text_type" => $inputTextType,
            "output_language_level" => "plain",
            "is_new_lines" => true,
            "separator" => "none",
            "embolden_negative" => false,
            "translation_language"=> $parameter->targetLang,
            "is_test" => false
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Api-Key ' . $this->authKey
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));

        return $curl;
    }
}
