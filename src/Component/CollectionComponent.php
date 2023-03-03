<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\UI\Renderable\Renderable;

final class CollectionComponent extends Control
{

	private ?Renderable $separator = null;

	/** @var Renderable[] */
	private array $components = [];

	/**
	 * @param array<Renderable|null> $components
	 */
	public function __construct(
		array $components,
		private ?Renderable $container = null,
	)
	{
		foreach ($components as $component) {
			if (!$component) {
				continue;
			}

			$component->install($this);

			$this->components[] = $component;
		}

		$this->container?->install($this);
	}

	public function setSeparator(?Renderable $separator): static
	{
		$this->separator = $separator;

		return $this;
	}

	public function render(): void
	{
		/** @var Template $template */
		$template = $this->getTemplate();

		$template->render(__DIR__ . '/templates/wrap.latte', [
			'components' => $this->components,
			'container' => $this->container,
			'separator' => $this->separator,
		]);
	}

}
