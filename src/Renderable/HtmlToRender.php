<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;
use Nette\Utils\Html;

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

	public function render(Template $template, array $options = []): void
	{
		if (($options['start'] ?? false) === true) {
			echo $this->html->startTag();

		} else if (($options['end'] ?? false) === true) {
			echo $this->html->endTag();

		} else {
			echo $this->html;

		}
	}

}
