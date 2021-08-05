<?php


namespace WikibaseSolutions\CypherDSL\Clauses;


class Match
{
	/**
	 * @var string
	 */
	private $variable;

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var array
	 */
	private $properties;

	public function __construct(string $variable, string $label, array $properties)
	{
		$this->variable = $variable;
		$this->label = $label;
		$this->properties = $properties;
	}
}