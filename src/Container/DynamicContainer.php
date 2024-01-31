<?php declare(strict_types = 1);

namespace WebChemistry\UI\Container;

use Nette\Application\UI\Component;
use Nette\ComponentModel\IComponent;

/**
 * @template TValue
 */
class DynamicContainer extends Component
{

	/** @var string[] */
	private array $componentsToDisplay = [];

	/** @var callable(TValue): IComponent */
	private $factory;

	/**
	 * @param DynamicContainerSerializer<TValue> $serializer
	 * @param TValue[] $values
	 * @param callable(TValue): IComponent $factory
	 */
	public function __construct(
		private DynamicContainerSerializer $serializer,
		array $values,
		callable $factory,
	)
	{
		$this->factory = $factory;

		foreach ($values as $value) {
			$this->addValue($value);
		}
	}

	/**
	 * @param TValue $value
	 * @return self<TValue>
	 */
	public function addValue(mixed $value): self
	{
		$this->addComponent(
			($this->factory)($value),
			$this->componentsToDisplay[] = $this->serializer->serialize($value),
		);

		return $this;
	}

	protected function createComponent(string $name): ?IComponent
	{
		$value = $this->serializer->unserialize($name);

		if ($value) {
			$component = ($this->factory)($value);

			$this->addComponent($component, $name);

			return $component;
		}

		return parent::createComponent($name);
	}

	/**
	 * @return IComponent[]
	 */
	public function getComponentsToDisplay(): array
	{
		$controls = [];

		foreach ($this->componentsToDisplay as $item) {
			$controls[] = $this[$item];
		}
		
		return $controls;
	}

}
