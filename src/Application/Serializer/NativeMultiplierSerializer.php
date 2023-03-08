<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

/**
 * @implements MultiplierSerializer<string|int>
 */
final class NativeMultiplierSerializer implements MultiplierSerializer
{

	/**
	 * @param string|int $value
	 * @return string
	 */
	public function serialize(mixed $value): string
	{
		return (string) $value;
	}

	public function unserialize(string $id): string
	{
		return $id;
	}

}
