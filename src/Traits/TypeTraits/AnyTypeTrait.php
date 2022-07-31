<?php

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Alias;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This trait provides a default implementation to satisfy the "AnyType" interface.
 *
 * @implements AnyType
 */
trait AnyTypeTrait
{
	use CastTrait;

	/**
	 * @inheritDoc
	 */
	public function alias($right): Alias
	{
		return new Alias($this, self::toVariable($right));
	}

	/**
	 * @inheritDoc
	 */
	public function equals($right, bool $insertParentheses = true): Equality
	{
		return new Equality($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function notEquals($right, bool $insertParentheses = true): Inequality
	{
		return new Inequality($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function gt($right, bool $insertParentheses = true): GreaterThan
	{
		return new GreaterThan($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function gte($right, bool $insertParentheses = true): GreaterThanOrEqual
	{
		return new GreaterThanOrEqual($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function lt($right, bool $insertParentheses = true): LessThan
	{
		return new LessThan($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function lte($right, bool $insertParentheses = true): LessThanOrEqual
	{
		return new LessThanOrEqual($this, self::toAnyType($right), $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function isNull(bool $insertParentheses = true): IsNull
	{
		return new IsNull($this, $insertParentheses);
	}

	/**
	 * @inheritDoc
	 */
	public function isNotNull(bool $insertParentheses = true): IsNotNull
	{
		return new IsNotNull($this, $insertParentheses);
	}
}