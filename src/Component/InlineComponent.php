<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;

final class InlineComponent extends Control
{

	/**
	 * @param mixed[] $parameters
	 * @param array<string, IComponent|null> $components
	 */
	public function __construct(
		private string $templateFile,
		private array $parameters = [],
		array $components = [],
	)
	{
		foreach ($components as $name => $component) {
			if ($component) {
				$this->addComponent($component, $name);
			}
		}
	}

	public function render(): void {
		/** @var Template $template */
		$template = $this->createTemplate();

		$template->render($this->templateFile, $this->parameters);
	}

}
