<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ModuloDivision;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * @coversNothing
 */
final class PrecedenceTest extends TestCase
{
    public function testBooleanLogicOrXorAnd(): void
    {
        $true = new Boolean(true);
        $false = new Boolean(false);

        $expr = new Disjunction($true, new ExclusiveDisjunction($true, $false));
        $this->assertSame('true OR true XOR false', $expr->toQuery());

        $expr = new ExclusiveDisjunction(new Disjunction($true, $false), $true);
        $this->assertSame('(true OR false) XOR true', $expr->toQuery());

        $expr = new Disjunction($true, new Conjunction($true, $false));
        $this->assertSame('true OR true AND false', $expr->toQuery());

        $expr = new Conjunction(new Disjunction($true, $false), $true);
        $this->assertSame('(true OR false) AND true', $expr->toQuery());

        $expr = new ExclusiveDisjunction($true, new Conjunction($true, $false));
        $this->assertSame('true XOR true AND false', $expr->toQuery());

        $expr = new Conjunction(new ExclusiveDisjunction($true, $false), $true);
        $this->assertSame('(true XOR false) AND true', $expr->toQuery());

        $expr = new Disjunction(new Disjunction($true, $false), $true);
        $this->assertSame('(true OR false) OR true', $expr->toQuery());

        $expr = new Conjunction(new Conjunction($true, $false), $true);
        $this->assertSame('(true AND false) AND true', $expr->toQuery());
    }

    public function testBooleanLogicWithComparison(): void
    {
        $var = new Variable('x');
        $num = new Integer(5);

        $expr = new Disjunction(new Equality($var, $num), new Equality($var, $num));
        $this->assertSame('x = 5 OR x = 5', $expr->toQuery());

        $expr = new Equality(new Disjunction(new Boolean(true), new Boolean(false)), new Boolean(true));
        $this->assertSame('(true OR false) = true', $expr->toQuery());

        $expr = new Conjunction(new LessThan($var, $num), new GreaterThan($var, $num));
        $this->assertSame('x < 5 AND x > 5', $expr->toQuery());

        $expr = new LessThan(new Conjunction(new Boolean(true), new Boolean(false)), new Boolean(true));
        $this->assertSame('(true AND false) < true', $expr->toQuery());
    }

    public function testComparisonWithAdditive(): void
    {
        $num1 = new Integer(10);
        $num2 = new Integer(5);
        $num3 = new Integer(3);

        $expr = new Equality(new Addition($num1, $num2), $num3);
        $this->assertSame('10 + 5 = 3', $expr->toQuery());

        $expr = new LessThan(new Subtraction($num1, $num2), $num3);
        $this->assertSame('10 - 5 < 3', $expr->toQuery());

        $expr = new GreaterThan(new Addition($num1, $num2), new Subtraction($num1, $num2));
        $this->assertSame('10 + 5 > 10 - 5', $expr->toQuery());

        $expr = new Equality(new Inequality($num1, $num2), new Boolean(true));
        $this->assertSame('10 <> 5 = true', $expr->toQuery());
    }

    public function testAdditiveWithMultiplicative(): void
    {
        $num = new Float_(10.0);

        $expr = new Addition($num, new Multiplication($num, $num));
        $this->assertSame('10.0 + 10.0 * 10.0', $expr->toQuery());

        $expr = new Multiplication($num, new Addition($num, $num));
        $this->assertSame('10.0 * (10.0 + 10.0)', $expr->toQuery());

        $expr = new Subtraction($num, new Division($num, $num));
        $this->assertSame('10.0 - 10.0 / 10.0', $expr->toQuery());

        $expr = new Division($num, new Subtraction($num, $num));
        $this->assertSame('10.0 / (10.0 - 10.0)', $expr->toQuery());

        $expr = new Addition($num, new ModuloDivision($num, $num));
        $this->assertSame('10.0 + 10.0 % 10.0', $expr->toQuery());

        $expr = new ModuloDivision($num, new Addition($num, $num));
        $this->assertSame('10.0 % (10.0 + 10.0)', $expr->toQuery());
    }

    public function testAdditiveSamePrecedence(): void
    {
        $num = new Float_(5.0);

        $expr = new Addition(new Addition($num, $num), $num);
        $this->assertSame('(5.0 + 5.0) + 5.0', $expr->toQuery());

        $expr = new Subtraction(new Subtraction($num, $num), $num);
        $this->assertSame('(5.0 - 5.0) - 5.0', $expr->toQuery());

        $expr = new Addition(new Subtraction($num, $num), $num);
        $this->assertSame('(5.0 - 5.0) + 5.0', $expr->toQuery());

        $expr = new Subtraction(new Addition($num, $num), $num);
        $this->assertSame('(5.0 + 5.0) - 5.0', $expr->toQuery());
    }

    public function testMultiplicativeWithExponentiation(): void
    {
        $num = new Float_(2.0);

        $expr = new Multiplication($num, new Exponentiation($num, $num));
        $this->assertSame('2.0 * 2.0 ^ 2.0', $expr->toQuery());

        $expr = new Exponentiation($num, new Multiplication($num, $num));
        $this->assertSame('2.0 ^ (2.0 * 2.0)', $expr->toQuery());

        $expr = new Division($num, new Exponentiation($num, $num));
        $this->assertSame('2.0 / 2.0 ^ 2.0', $expr->toQuery());

        $expr = new Exponentiation($num, new Division($num, $num));
        $this->assertSame('2.0 ^ (2.0 / 2.0)', $expr->toQuery());

        $expr = new ModuloDivision($num, new Exponentiation($num, $num));
        $this->assertSame('2.0 % 2.0 ^ 2.0', $expr->toQuery());

        $expr = new Exponentiation($num, new ModuloDivision($num, $num));
        $this->assertSame('2.0 ^ (2.0 % 2.0)', $expr->toQuery());
    }

    public function testMultiplicativeSamePrecedence(): void
    {
        $num = new Float_(4.0);

        $expr = new Multiplication(new Multiplication($num, $num), $num);
        $this->assertSame('(4.0 * 4.0) * 4.0', $expr->toQuery());

        $expr = new Division(new Division($num, $num), $num);
        $this->assertSame('(4.0 / 4.0) / 4.0', $expr->toQuery());

        $expr = new ModuloDivision(new ModuloDivision($num, $num), $num);
        $this->assertSame('(4.0 % 4.0) % 4.0', $expr->toQuery());

        $expr = new Multiplication(new Division($num, $num), $num);
        $this->assertSame('(4.0 / 4.0) * 4.0', $expr->toQuery());

        $expr = new Division(new ModuloDivision($num, $num), $num);
        $this->assertSame('(4.0 % 4.0) / 4.0', $expr->toQuery());
    }

    public function testExponentiationSamePrecedence(): void
    {
        $num = new Float_(3.0);

        $expr = new Exponentiation(new Exponentiation($num, $num), $num);
        $this->assertSame('(3.0 ^ 3.0) ^ 3.0', $expr->toQuery());
    }

    public function testNegationWithBinaryOperators(): void
    {
        $true = new Boolean(true);
        $false = new Boolean(false);
        $num1 = new Integer(5);
        $num2 = new Integer(10);

        $expr = new Negation(new Disjunction($true, $false));
        $this->assertSame('NOT (true OR false)', $expr->toQuery());

        $expr = new Disjunction(new Negation($true), $false);
        $this->assertSame('NOT true OR false', $expr->toQuery());

        $expr = new Negation(new Conjunction($true, $false));
        $this->assertSame('NOT (true AND false)', $expr->toQuery());

        $expr = new Conjunction(new Negation($true), $false);
        $this->assertSame('NOT true AND false', $expr->toQuery());

        $expr = new Negation(new Equality($num1, $num2));
        $this->assertSame('NOT 5 = 10', $expr->toQuery());

        $expr = new Equality(new Negation($true), $false);
        $this->assertSame('(NOT true) = false', $expr->toQuery());

        $expr = new Negation(new LessThan(new Addition($num1, $num2), $num2));
        $this->assertSame('NOT 5 + 10 < 10', $expr->toQuery());
    }

    public function testUnaryMinusWithBinaryOperators(): void
    {
        $num1 = new Float_(5.0);
        $num2 = new Float_(3.0);

        $expr = new UnaryMinus(new Addition($num1, $num2));
        $this->assertSame('- (5.0 + 3.0)', $expr->toQuery());

        $expr = new Addition(new UnaryMinus($num1), $num2);
        $this->assertSame('- 5.0 + 3.0', $expr->toQuery());

        $expr = new UnaryMinus(new Multiplication($num1, $num2));
        $this->assertSame('- (5.0 * 3.0)', $expr->toQuery());

        $expr = new Multiplication(new UnaryMinus($num1), $num2);
        $this->assertSame('- 5.0 * 3.0', $expr->toQuery());

        $expr = new UnaryMinus(new Exponentiation($num1, $num2));
        $this->assertSame('- (5.0 ^ 3.0)', $expr->toQuery());

        $expr = new Exponentiation(new UnaryMinus($num1), $num2);
        $this->assertSame('- 5.0 ^ 3.0', $expr->toQuery());
    }

    public function testPostfixUnaryWithBinaryOperators(): void
    {
        $var = new Variable('x');
        $num = new Integer(5);

        $expr = new IsNull(new Addition($num, $num));
        $this->assertSame('(5 + 5) IS NULL', $expr->toQuery());

        $expr = new Equality(new IsNull($var), new Boolean(true));
        $this->assertSame('x IS NULL = true', $expr->toQuery());

        $expr = new IsNotNull(new Disjunction(new Boolean(true), new Boolean(false)));
        $this->assertSame('(true OR false) IS NOT NULL', $expr->toQuery());

        $expr = new Disjunction(new IsNotNull($var), new Boolean(false));
        $this->assertSame('x IS NOT NULL OR false', $expr->toQuery());

        $expr = new IsNull(new Equality($var, $num));
        $this->assertSame('(x = 5) IS NULL', $expr->toQuery());

        $expr = new Equality(new IsNull($var), new Boolean(true));
        $this->assertSame('x IS NULL = true', $expr->toQuery());
    }

    public function testFunctionalWithAllLevels(): void
    {
        $str = new Variable('name');
        $pattern = new Variable('pattern');
        $list = new Variable('list');
        $val = new Variable('val');

        $expr = new Disjunction(new Contains($str, $pattern), new Boolean(false));
        $this->assertSame('name CONTAINS pattern OR false', $expr->toQuery());

        $expr = new Equality(new Contains($str, $pattern), new Boolean(true));
        $this->assertSame('name CONTAINS pattern = true', $expr->toQuery());

        $expr = new Disjunction(new In($val, $list), new Boolean(false));
        $this->assertSame('val IN list OR false', $expr->toQuery());

        $expr = new Equality(new In($val, $list), new Boolean(true));
        $this->assertSame('val IN list = true', $expr->toQuery());

        $expr = new StartsWith($str, $str);
        $this->assertSame('name STARTS WITH name', $expr->toQuery());

        $expr = new Equality(new StartsWith($str, $pattern), new Boolean(true));
        $this->assertSame('name STARTS WITH pattern = true', $expr->toQuery());
    }

    public function testNestedUnaryOperators(): void
    {
        $true = new Boolean(true);
        $num = new Float_(5.0);
        $var = new Variable('x');

        $expr = new Negation(new Negation($true));
        $this->assertSame('NOT (NOT true)', $expr->toQuery());

        $expr = new UnaryMinus(new UnaryMinus($num));
        $this->assertSame('- (- 5.0)', $expr->toQuery());

        $expr = new Negation(new Equality(new UnaryMinus($num), $num));
        $this->assertSame('NOT - 5.0 = 5.0', $expr->toQuery());

        $expr = new IsNull(new UnaryMinus($num));
        $this->assertSame('(- 5.0) IS NULL', $expr->toQuery());

        $expr = new Negation(new IsNull($var));
        $this->assertSame('NOT x IS NULL', $expr->toQuery());

        $expr = new IsNotNull(new IsNull($var));
        $this->assertSame('(x IS NULL) IS NOT NULL', $expr->toQuery());
    }

    public function testThreeLevelNesting(): void
    {
        $num = new Float_(2.0);
        $true = new Boolean(true);
        $false = new Boolean(false);

        $expr = new Disjunction(
            new Conjunction(
                new Equality(new Addition($num, $num), $num),
                $true
            ),
            $false
        );
        $this->assertSame('2.0 + 2.0 = 2.0 AND true OR false', $expr->toQuery());

        $expr = new Multiplication(
            new Addition(
                new Subtraction($num, $num),
                $num
            ),
            $num
        );
        $this->assertSame('((2.0 - 2.0) + 2.0) * 2.0', $expr->toQuery());

        $expr = new Exponentiation(
            new Multiplication(
                new Addition($num, $num),
                $num
            ),
            $num
        );
        $this->assertSame('((2.0 + 2.0) * 2.0) ^ 2.0', $expr->toQuery());
    }

    public function testMixedUnaryBinaryNesting(): void
    {
        $num = new Float_(3.0);
        $true = new Boolean(true);
        $false = new Boolean(false);

        $expr = new Negation(
            new Disjunction(
                new LessThan(new UnaryMinus($num), $num),
                $false
            )
        );
        $this->assertSame('NOT (- 3.0 < 3.0 OR false)', $expr->toQuery());

        $expr = new LessThan(
            new LessThan($num, $num),
            $num
        );
        $this->assertSame('3.0 < 3.0 < 3.0', $expr->toQuery());

        $expr = new Multiplication(
            new UnaryMinus(new Exponentiation($num, $num)),
            new UnaryMinus($num)
        );
        $this->assertSame('- (3.0 ^ 3.0) * - 3.0', $expr->toQuery());

        $expr = new Conjunction(
            new Negation(
                new GreaterThan(
                    new UnaryMinus($num),
                    $num
                )
            ),
            $true
        );
        $this->assertSame('NOT - 3.0 > 3.0 AND true', $expr->toQuery());
    }

    public function testCrossPrecedenceCombinations(): void
    {
        $num = new Integer(1);
        $true = new Boolean(true);
        $var = new Variable('x');

        $expr = new Disjunction($true, new Equality(new Multiplication($num, $num), $num));
        $this->assertSame('true OR 1 * 1 = 1', $expr->toQuery());
        $expr = new Equality(new Disjunction($true, $true), $true);
        $this->assertSame('(true OR true) = true', $expr->toQuery());

        $expr = new ExclusiveDisjunction($true, new GreaterThan(new Exponentiation($num, $num), $num));
        $this->assertSame('true XOR 1 ^ 1 > 1', $expr->toQuery());

        $expr = new Conjunction(new LessThan(new Addition($num, $num), $num), $true);
        $this->assertSame('1 + 1 < 1 AND true', $expr->toQuery());
        $expr = new LessThan(new Conjunction($true, $true), $true);
        $this->assertSame('(true AND true) < true', $expr->toQuery());

        $expr = new Negation(new IsNull($var));
        $this->assertSame('NOT x IS NULL', $expr->toQuery());
        $expr = new IsNull(new Negation($true));
        $this->assertSame('(NOT true) IS NULL', $expr->toQuery());

        $expr = new LessThan(new UnaryMinus($num), $num);
        $this->assertSame('- 1 < 1', $expr->toQuery());
        $expr = new UnaryMinus(new Addition($num, $num));
        $this->assertSame('- (1 + 1)', $expr->toQuery());

        $expr = new LessThan(new Addition($num, $num), new Contains($var, $var));
        $this->assertSame('1 + 1 < x CONTAINS x', $expr->toQuery());
    }

    public function testAllSamePrecedenceCombinations(): void
    {
        $num = new Float_(7.0);
        $true = new Boolean(true);
        $var = new Variable('y');

        $expr = new Disjunction(new Disjunction($true, $true), $true);
        $this->assertSame('(true OR true) OR true', $expr->toQuery());

        $expr = new ExclusiveDisjunction(new ExclusiveDisjunction($true, $true), $true);
        $this->assertSame('(true XOR true) XOR true', $expr->toQuery());

        $expr = new Conjunction(new Conjunction($true, $true), $true);
        $this->assertSame('(true AND true) AND true', $expr->toQuery());

        $expr = new Negation(new Negation($true));
        $this->assertSame('NOT (NOT true)', $expr->toQuery());

        $expr = new Equality(new LessThan($num, $num), $true);
        $this->assertSame('7.0 < 7.0 = true', $expr->toQuery());

        $expr = new LessThanOrEqual(new GreaterThanOrEqual($num, $num), $num);
        $this->assertSame('7.0 >= 7.0 <= 7.0', $expr->toQuery());

        $expr = new Addition(new Subtraction($num, $num), $num);
        $this->assertSame('(7.0 - 7.0) + 7.0', $expr->toQuery());

        $expr = new Multiplication(new Division($num, $num), $num);
        $this->assertSame('(7.0 / 7.0) * 7.0', $expr->toQuery());

        $expr = new ModuloDivision(new Multiplication($num, $num), $num);
        $this->assertSame('(7.0 * 7.0) % 7.0', $expr->toQuery());

        $expr = new Exponentiation(new Exponentiation($num, $num), $num);
        $this->assertSame('(7.0 ^ 7.0) ^ 7.0', $expr->toQuery());

        $expr = new UnaryMinus(new UnaryMinus($num));
        $this->assertSame('- (- 7.0)', $expr->toQuery());

        $expr = new IsNull(new IsNotNull($var));
        $this->assertSame('(y IS NOT NULL) IS NULL', $expr->toQuery());

        $expr = new Equality(new Contains($var, $var), new StartsWith($var, $var));
        $this->assertSame('y CONTAINS y = y STARTS WITH y', $expr->toQuery());
    }

    public function testChainedComparison(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(1);

        $expr = new GreaterThan(new GreaterThan($a, $b), $c);
        $this->assertSame("10 > 5 > 1", $expr->toQuery());
    }

    public function testChainedComparisonRhs(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(1);

        $expr = new GreaterThan($a, new GreaterThan($b, $c));
        $this->assertSame("10 > 5 > 1", $expr->toQuery());
    }

    public function testChainedComparisonRhsCheck(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(1);
        $d = new Integer(0);

        $expr = new GreaterThan($a, new GreaterThan($b, new GreaterThan($c, $d)));
        $this->assertSame("10 > 5 > 1 > 0", $expr->toQuery());
    }

    public function testChainedMixedComparisons(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(5);

        $expr = new GreaterThanOrEqual(new GreaterThan($a, $b), $c);
        $this->assertSame("10 > 5 >= 5", $expr->toQuery());
    }

    public function testChainedInequality(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(10);

        $expr = new Inequality(new Inequality($a, $b), $c);
        $this->assertSame("10 <> 5 <> 10", $expr->toQuery());
    }

    public function testChainedEquality(): void
    {
        $a = new Integer(1);
        $b = new Integer(1);
        $c = new Integer(1);

        $expr = new Equality(new Equality($a, $b), $c);
        $this->assertSame("1 = 1 = 1", $expr->toQuery());

        $expr = new Equality($a, new Equality($b, $c));
        $this->assertSame("1 = 1 = 1", $expr->toQuery());
    }
}
