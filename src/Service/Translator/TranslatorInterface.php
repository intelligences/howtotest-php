<?php

declare(strict_types=1);

namespace Intelligences\HowtotestPhp\Service\Translator;

interface TranslatorInterface
{
    /**
     * @param string   $text
     * @param Language $from
     * @param Language $to
     *
     * @return string
     *
     * @throws Exception\TranslationError
     */
    public function translate(string $text, Language $from, Language $to): string;
}