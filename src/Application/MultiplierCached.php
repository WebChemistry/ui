<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application;

use Nette\Application\UI\Control;

/**
 * @template T
 */
final class MultiplierCached
{

	/**
	 * @param T $value
	 */
	public function __construct(
		public readonly mixed $value,
		public readonly Control $control,
	)
	{
	}

}
