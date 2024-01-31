<?php declare(strict_types = 1);

namespace WebChemistry\UI\Container;

/**
 * @template T
 */
interface DynamicContainerSerializer
{

	/**
	 * @return T|null
	 */
	public function unserialize(string $name): mixed;

	public function serialize(mixed $value): string;

}
