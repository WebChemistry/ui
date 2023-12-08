<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Renderable as NetteRenderable;
use Nette\ComponentModel\Container;
use WebChemistry\UI\Template\TemplateOptions;

final class ContainerToRender implements Renderable
{

	public function __construct(
		private Container $container,
		private string $name,
		private bool $deep = false,
	)
	{
	}

	public function install(Control $control): void
	{
		$control->addComponent($this->container, $this->name);
	}

	public function render(Template $template, TemplateOptions $options, bool $core = false): void
	{
		foreach ($this->container->getComponents($this->deep, NetteRenderable::class) as $component) {
			if (!method_exists($component, 'render')) {
				throw new LogicException(sprintf('Control %s does not have method render.', $component::class));
			}

			$options->applyHooks(fn () => $component->render(), $core);
		}
	}

}
