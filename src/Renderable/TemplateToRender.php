<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;
use WebChemistry\UI\Template\TemplateOptions;

final class TemplateToRender implements Renderable
{

	/**
	 * @param mixed[]|object $params
	 */
	public function __construct(
		private string $template,
		private array|object $params,
	)
	{
	}

	public function install(Control $control): void
	{
	}

	public function render(Template $template, TemplateOptions $options): void
	{
		$options->applyHooks(fn () => $template->getEngine()->render($this->template, $this->params));
	}

}
