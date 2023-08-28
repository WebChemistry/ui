<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use WebChemistry\UI\Hook\AfterRenderHookArguments;
use WebChemistry\UI\Hook\BeforeRenderHookArguments;
use WebChemistry\UI\Renderable\ControlToRender;
use WebChemistry\UI\Renderable\Renderable;
use WebChemistry\UI\Template\TemplateOptions;

final class CollectionComponent extends Control
{

	private ?Renderable $separator = null;

	/** @var Renderable[] */
	private array $components = [];

	/** @var array<callable(BeforeRenderHookArguments): void> */
	private array $beforeRenderHooks = [];

	/** @var array<callable(AfterRenderHookArguments): void> */
	private array $afterRenderHooks = [];

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

	public function addPositionalRenderable(int $position, Renderable $renderable): static
	{
		$position = max(0, $position);
		$rendered = false;

		$renderable->install($this);

		$this->beforeRenderHooks[] = function (BeforeRenderHookArguments $args) use ($position, $renderable, &$rendered): void {
			if ($rendered) {
				return;
			}

			if ($args->counter === $position) {
				$args->options->render($renderable);

				$rendered = true;

			} else if ($args->counter === null) {
				$args->options->render($renderable);

				$rendered = true;
			}
		};

		return $this;
	}

	/**
	 * @param callable(BeforeRenderHookArguments): void $callback
	 * @return static
	 */
	public function addBeforeRenderHook(callable $callback): static
	{
		$this->beforeRenderHooks[] = $callback;

		return $this;
	}

	/**
	 * @param callable(AfterRenderHookArguments): void $callback
	 * @return static
	 */
	public function addAfterRenderHook(callable $callback): static
	{
		$this->afterRenderHooks[] = $callback;

		return $this;
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

		$template->render(__DIR__ . '/templates/collection.latte', [
			'components' => $this->components,
			'container' => $this->container,
			'separator' => $this->separator,
			'options' => new TemplateOptions(
				$this->beforeRenderHooks,
				$this->afterRenderHooks,
			),
		]);
	}

}
