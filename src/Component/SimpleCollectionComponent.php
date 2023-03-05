<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;
use LogicException;

final class SimpleCollectionComponent extends Control
{

	private bool $isAnchored = false;

	private bool $empty = true;

	public function __construct()
	{
		$this->onAnchor[] = function (): void {
			$this->isAnchored = true;
		};
	}

	public function isEmpty(): bool
	{
		return $this->empty;
	}

	public function addComponent(IComponent $component, ?string $name, string $insertBefore = null): static
	{
		if ($this->isAnchored) {
			throw new LogicException('Component is anchored, adding new components is not recommended.');
		}

		$this->empty = false;

		return parent::addComponent($component, $name, $insertBefore);
	}

	public function render(): void
	{
		/** @var Template $template */
		$template = $this->getTemplate();

		$template->render(__DIR__ . '/templates/simpleCollection.latte', [
			'components' => $this->getComponents(),
		]);
	}

}
