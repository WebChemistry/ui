<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;

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

	/**
	 * @param mixed[] $options
	 */
	public function render(Template $template, array $options = []): void
	{
		$template->getEngine()->render($this->template, $this->params);
	}

}
