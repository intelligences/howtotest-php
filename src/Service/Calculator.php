<?php

declare(strict_types=1);

namespace Intelligences\HowtotestPhp\Service;

class Calculator
{
    public function sum(float $a, float $b): float
    {
        return $a + $b;
    }

    public function subtract(float $from, float $value): float
    {
        return $from - $value;
    }

    public function divide(float $value, float $divider): float
    {
        if ($divider === 0.) {
            throw new \DivisionByZeroError("Can not divide by zero");
        }

        return $value / $divider;
    }

    public function multiply(float $a, float $b): float
    {
        return $a * $b;
    }
}
