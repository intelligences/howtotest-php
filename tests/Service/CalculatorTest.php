<?php

declare(strict_types=1);

namespace Tests\Intelligences\HowtotestPhp\Service;

use Intelligences\HowtotestPhp\Service\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new Calculator();
    }

    // Checks sum of two integers
    public function testSum(): void
    {
        self::assertEquals(9, $this->calculator->sum(6, 3));
    }

    // Checks subtraction of two integers
    public function testSubtract(): void
    {
        self::assertEquals(3, $this->calculator->subtract(6, 3));
    }

    // Checks multiplication of two integers
    public function testMultiply(): void
    {
        self::assertEquals(18, $this->calculator->multiply(6, 3));
    }

    // Check division of two integers
    public function testDivide(): void
    {
        self::assertEquals(2, $this->calculator->divide(6, 3));
    }

    // Testing edge cases is also part of development
    public function testDivisionByZero(): void
    {
        $this->expectException(\DivisionByZeroError::class);

        self::assertEquals(2, $this->calculator->divide(6, 0));
    }

    /**
     * Better version of testSum
     * It runs test below with each set of data from dataProvider
     *
     * @param float $a
     * @param float $b
     * @param float $expectedResult
     *
     *
     * @dataProvider summProvider
     *
     * @see          testSum
     *
     */
    public function testSumProperly(float $a, float $b, float $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->calculator->sum($a, $b));
    }

    /**
     * Data provider is a set of data for separate test cases.
     * It is represented as an iterable list of arguments which will be passed to test-function.
     * It is a good idea to set keys for those lists to simplify understanding of cases but that is not necessary.
     *
     * Advanced tip: one can use generators instead of array as generator is an iterable type too.
     * It is also easier to read.
     * yield "case1" => ["arg1", "arg2"];
     * yield "case2" => ["arg1", "arg2"];
     *
     * instead of
     *
     * return [
     *    "case1" => ["arg1", "arg2"],
     *    "case2" => ["arg1", "arg2"],
     * ];
     *
     * @return iterable
     */
    public function summProvider(): iterable
    {
        return [
            "positive integers"      => [6, 3, 9],
            "one negative integer"   => [6, -3, 3],
            "both negative integers" => [-6, -3, -9],
            "float values"           => [1.23, 56.789, 58.019],
        ];
    }
}
