<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;
use LogicException;
use Nette\Utils\Html;

final class SimpleCollectionComponent extends Control
{

	private bool $lazyLoading = false;

	private bool $isAnchored = false;

	private bool $empty = true;

	private ?Html $wrapper = null;

	/**
	 * @param array<string, IComponent|null> $components
	 */
	public function __construct(array $components = [], Html|string|null $wrapper = null)
	{
		$this->onAnchor[] = function (): void {
			$this->isAnchored = true;
		};

		foreach ($components as $name => $component) {
			if ($component) {
				$this->addComponent($component, $name);
			}
		}

		$this->setWrapper($wrapper);
	}

	public function allowLazyLoading(): self
	{
		$this->lazyLoading = true;

		return $this;
	}

	public function isEmpty(): bool
	{
		return $this->empty;
	}

	public function clear(): void
	{
		$this->empty = true;

		foreach ($this->getComponents() as $component) {
			$this->removeComponent($component);
		}
	}

	public function prependComponent(IComponent $component, ?string $name): static
	{
		$components = iterator_to_array($this->getComponents());

		if (!$components) {
			$insertBefore = null;
		} else {
			$insertBefore = array_key_first($components);
		}

		return $this->addComponent($component, $name, $insertBefore);
	}

	public function addComponent(IComponent $component, ?string $name, string $insertBefore = null): static
	{
		if ($this->isAnchored) {
			throw new LogicException('Component is anchored, adding new components is not recommended.');
		}

		$this->empty = false;

		return parent::addComponent($component, $name, $insertBefore);
	}

	/**
	 * @param array<string, IComponent|null> $components
	 */
	public function addGroup(array $components, string $name, Html|string|null $wrapper = null, string $insertBefore = null): SimpleCollectionComponent
	{
		$collection = new self($components, $wrapper);

		$this->addComponent($collection, $name);

		return $collection;
	}

	public function setWrapper(Html|string|null $wrapper): self
	{
		if (is_string($wrapper)) {
			$this->wrapper = Html::el('div', ['class' => $wrapper]);
		} else {
			$this->wrapper = $wrapper;
		}

		return $this;
	}

	public function render(): void
	{
		/** @var Template $template */
		$template = $this->getTemplate();

		$template->render(__DIR__ . '/templates/simpleCollection.latte', [
			'components' => $this->getComponents(),
			'wrapper' => $this->wrapper,
		]);
	}

	public function renderLazyLink(): void
	{
		if (!$this->lazyLoading) {
			throw new LogicException('Lazy loading is not allowed.');
		}

		echo $this->link('load!');
	}

	public function handleLoad(): void
	{
		$this->render();

		$this->getPresenter()->terminate();
	}

}
