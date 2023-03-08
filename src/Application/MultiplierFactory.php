<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application;

use Nette\Application\UI\Control;
use WebChemistry\UI\Application\Serializer\MultiplierSerializer;

/**
 * @template T
 */
final class MultiplierFactory
{

	/**
	 * @param MultiplierSerializer<T> $serializer
	 */
	public function __construct(
		private MultiplierSerializer $serializer,
	)
	{
	}

	/**
	 * @param callable(T): Control $factory
	 * @param iterable<MultiplierCached<T>> $cache
	 * @return Multiplier<T>
	 */
	public function create(callable $factory, iterable $cache = []): Multiplier
	{
		$multiplier = new Multiplier(function (string $id) use ($factory): ?Control {
			$value = $this->serializer->unserialize($id);

			return $value === null ? null : $factory($value);
		}, $this->serializer);

		foreach ($cache as $item) {
			$multiplier->addComponent($item->control, $this->serializer->serialize($item->value));
		}

		return $multiplier;
	}

}
