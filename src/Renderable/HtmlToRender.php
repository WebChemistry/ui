<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;
use Nette\Utils\Html;
use WebChemistry\UI\Template\TemplateOptions;

final class HtmlToRender implements Renderable
{

	public function __construct(
		private Html $html,
	)
	{
	}

	public function install(Control $control): void
	{
	}

	public function render(Template $template, TemplateOptions $options, bool $core = false): void
	{
		if ($options->isStartTag()) {
			echo $this->html->startTag();

		} else if ($options->isEndTag()) {
			echo $this->html->endTag();

		} else {
			echo $options->applyHooks(fn () => $this->html->toHtml(), $core);
		}
	}

}
