<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use LogicException;
use Nette\Application\UI\Control;
use WebChemistry\UI\Container\DynamicContainer;
use WebChemistry\UI\Template\TemplateOptions;

/**
 * @template TValue
 */
final class DynamicContainerToRender implements Renderable
{

	/**
	 * @param DynamicContainer<TValue> $container
	 */
	public function __construct(
		private DynamicContainer $container,
		private string $name,
	)
	{
	}

	public function install(Control $control): void
	{
		$control->addComponent($this->container, $this->name);
	}

	public function render(Template $template, TemplateOptions $options, bool $core = false): void
	{
		foreach ($this->container->getComponentsToDisplay() as $component) {
			if (!method_exists($component, 'render')) {
				throw new LogicException(sprintf('Control %s does not have method render.', $component::class));
			}

			$options->applyHooks(fn () => $component->render(), $core);
		}
	}

}
