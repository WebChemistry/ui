<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use Nette\Application\UI\Control;
use WebChemistry\UI\Template\TemplateOptions;

interface Renderable
{

	public function install(Control $control): void;

	public function render(Template $template, TemplateOptions $options): void;

}
