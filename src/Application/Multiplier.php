<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application;

use Nette\Application\UI\Component;
use Nette\ComponentModel\IComponent;
use WebChemistry\UI\Application\Serializer\MultiplierSerializer;

/**
 * @template T
 */
final class Multiplier extends Component
{

	/** @var callable(string, Multiplier<T>=): ?IComponent */
	private $factory;

	/**
	 * @param callable(string, Multiplier<T>=): ?IComponent $factory
	 * @param MultiplierSerializer<T> $serializer
	 */
	public function __construct(
		callable $factory,
		private MultiplierSerializer $serializer,
	)
	{
		$this->factory = $factory;
	}

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
