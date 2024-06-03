<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Latte\Runtime\Filters;
use Nette\Application\UI\Control;

final class TextualComponent extends Control
{

	public function __construct(
		private string $text,
	)
	{
	}

	public function render(): void
	{
		echo Filters::escapeHtml($this->text);
	}

}
