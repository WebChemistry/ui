<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

final class InlineComponent extends Control
{

	/**
	 * @param mixed[] $parameters
	 */
	public function __construct(
		private string $templateFile,
		private array $parameters,
	)
	{
	}

	public function render(): void {
		/** @var Template $template */
		$template = $this->createTemplate();
		
		$template->render($this->templateFile, $this->parameters);
	}

}
