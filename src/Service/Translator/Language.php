<?php

declare(strict_types=1);

namespace Intelligences\HowtotestPhp\Service\Translator;

class Language
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function english(): self
    {
        return new self('en');
    }

    public static function german(): self
    {
        return new self('de');
    }

    public function value(): string
    {
        return $this->value;
    }
}