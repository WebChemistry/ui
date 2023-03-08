<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

/**
 * @template T
 */
interface MultiplierSerializer
{

	/**
	 * @param string $id
	 * @return T|null
	 */
	public function unserialize(string $id): mixed;

	/**
	 * @param T $value
	 */
	public function serialize(mixed $value): string;

}
