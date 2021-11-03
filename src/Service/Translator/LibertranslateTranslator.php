<?php

declare(strict_types=1);

namespace Intelligences\HowtotestPhp\Service\Translator;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Translation service
 *
 * @link https://libretranslate.com/docs/
 */
class LibertranslateTranslator implements TranslatorInterface
{
    private const API_URL = 'https://libretranslate.com';

    private string $apiKey = "";

    private ClientInterface $httpClient;

    public function __construct(string $apiKey, ClientInterface $httpClient)
    {
        $this->apiKey     = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function translate(string $text, Language $from, Language $to): string
    {
        if ($text === "") {
            return "";
        }

        if ($from->value() === $to->value()) {
            return $text;
        }

        $request = $this->createRequest(
            'POST',
            self::API_URL . '/translate',
            [
                'q'       => $text,
                'source'  => $from->value(),
                'target'  => $to->value(),
                'format'  => 'text',
                'api_key' => $this->apiKey,
            ]
        );

        try {
            $response = $this->httpClient->sendRequest($request);
            if ($response->getStatusCode() !== 200) {
                throw new Exception\TranslationError("Something went wrong during translation");
            }

            $data = json_decode($response->getBody()->getContents());

            return $data->translatedText;
        } catch (ClientExceptionInterface $e) {
            throw new Exception\TranslationError("Unknown error occurred", 0, $e);
        }
    }

    private function createRequest(string $method, string $uri, array $data): RequestInterface
    {
        return new Request(
            $method,
            $uri,
            [
                'Content-Type' => 'application/json',
                'accept'       => 'application/json',
            ],
            json_encode($data),
        );
    }
}