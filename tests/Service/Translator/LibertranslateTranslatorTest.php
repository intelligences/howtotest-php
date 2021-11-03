<?php

declare(strict_types=1);

namespace Tests\Intelligences\HowtotestPhp\Service\Translator;

use Intelligences\HowtotestPhp\Service\Translator\Exception\TranslationError;
use Intelligences\HowtotestPhp\Service\Translator\Language;
use Intelligences\HowtotestPhp\Service\Translator\LibertranslateTranslator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class LibertranslateTranslatorTest extends TestCase
{
    private const API_KEY = "SomeApiKey";

    private ClientInterface $httpClient;

    private LibertranslateTranslator $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);

        $this->translator = new LibertranslateTranslator(self::API_KEY, $this->httpClient);
    }

    public function testAttemptToTranslateEmptyText(): void
    {
        $from = Language::english();
        $to   = Language::german();

        self::assertEquals("", $this->translator->translate("", $from, $to));
    }

    public function testAttemptToTranslateTextWhenSourceAndTargetLanguagesAreTheSame(): void
    {
        $from = Language::english();
        $to   = Language::english();
        $text = "Some text";

        self::assertEquals($text, $this->translator->translate($text, $from, $to));
    }

    public function testTranslateInvalidText(): void
    {
        // Arrange
        $from = Language::english();
        $to   = Language::german();
        $text = "-15%0xffc4d";

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                $this->callback(
                    function (RequestInterface $request): bool {
                        self::assertEquals('POST', $request->getMethod());
                        self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
                        self::assertEquals(['application/json'], $request->getHeader('accept'));
                        self::assertEquals('https://libretranslate.com/translate', (string)$request->getUri());
                        self::assertEquals(
                            '{"q":"-15%0xffc4d","source":"en","target":"de","format":"text","api_key":"SomeApiKey"}',
                            (string)$request->getBody()
                        );

                        return true;
                    }
                )
            )
            ->willThrowException($this->createMock(ClientExceptionInterface::class))
        ;

        // Assert (yes, in case of exceptions we have to write assertions before actions to catch them
        // One can use annotations for exception assertions but I think this way is cleaner
        $this->expectException(TranslationError::class);

        // Act
        $this->translator->translate($text, $from, $to);
    }

    public function testTranslationServerError(): void
    {
        // Arrange
        $from = Language::english();
        $to   = Language::german();
        $text = "Some text";

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                $this->callback(
                    function (RequestInterface $request): bool {
                        self::assertEquals('POST', $request->getMethod());
                        self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
                        self::assertEquals(['application/json'], $request->getHeader('accept'));
                        self::assertEquals('https://libretranslate.com/translate', (string)$request->getUri());
                        self::assertEquals(
                            '{"q":"Some text","source":"en","target":"de","format":"text","api_key":"SomeApiKey"}',
                            (string)$request->getBody()
                        );

                        return true;
                    }
                )
            )
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $response
            ->expects(self::atLeastOnce())
            ->method('getStatusCode')
            ->willReturn(300)
        ;

        // Assert
        $this->expectException(TranslationError::class);

        // Act
        $this->translator->translate($text, $from, $to);
    }

    public function testTranslate(): void
    {
        // Arrange
        $from = Language::english();
        $to   = Language::german();
        $text = "Some text";

        $this->httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                $this->callback(
                    function (RequestInterface $request): bool {
                        self::assertEquals('POST', $request->getMethod());
                        self::assertEquals(['application/json'], $request->getHeader('Content-Type'));
                        self::assertEquals(['application/json'], $request->getHeader('accept'));
                        self::assertEquals('https://libretranslate.com/translate', (string)$request->getUri());
                        self::assertEquals(
                            '{"q":"Some text","source":"en","target":"de","format":"text","api_key":"SomeApiKey"}',
                            (string)$request->getBody()
                        );

                        return true;
                    }
                )
            )
            ->willReturn($response = $this->createMock(ResponseInterface::class))
        ;

        $response
            ->expects(self::atLeastOnce())
            ->method('getStatusCode')
            ->willReturn(200)
        ;

        $response
            ->expects(self::atLeastOnce())
            ->method('getBody')
            ->willReturn($body = $this->createMock(StreamInterface::class))
        ;

        $body
            ->expects(self::once())
            ->method('getContents')
            ->willReturn('{"translatedText": "Etwas Text"}')
        ;

        // Act
        $translated = $this->translator->translate($text, $from, $to);

        // Assert
        self::assertEquals("Etwas Text", $translated);
    }
}