<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application;

use Nette\Application\UI\Component;
use Nette\ComponentModel\IComponent;
use WebChemistry\UI\Application\Serializer\MultiplierSerializer;
use WebChemistry\UI\Application\Serializer\NativeMultiplierSerializer;

/**
 * @template T
 */
final class Multiplier extends Component
{

	/** @var callable(string, Multiplier<T>=): ?IComponent */
	private $factory;

	/** @var MultiplierSerializer<T> */
	private MultiplierSerializer $serializer;

	/**
	 * @param callable(string, Multiplier<T>=): ?IComponent $factory
	 * @param MultiplierSerializer<T>|null $serializer
	 */
	public function __construct(
		callable $factory,
		?MultiplierSerializer $serializer,
	)
	{
		$this->factory = $factory;
		$this->serializer = $serializer ?? new NativeMultiplierSerializer(); // @phpstan-ignore-line
	}

	/**
	 * @param T $value
	 */
	public function get(mixed $value): IComponent
	{
		/** @var IComponent */
		return $this->getComponent($this->serializer->serialize($value));
	}

	protected function createComponent(string $name): ?IComponent
	{
		return ($this->factory)($name, $this);
	}

}
