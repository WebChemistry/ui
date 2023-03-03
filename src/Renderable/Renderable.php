<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;

interface Renderable
{

	public function install(Control $control): void;

	/**
	 * @param mixed[] $options
	 */
	public function render(Template $template, array $options = []): void;

}
