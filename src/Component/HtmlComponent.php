<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;
use Nette\Utils\Html;

final class HtmlComponent extends Control
{

	public function __construct(
		private Html $html,
	)
	{
	}

	public static function create(string $content, ?string $className = null): self
	{
		$el = Html::el('div');
		$el->setText($content);

		if ($className) {
			$el->class($className);
		}

		return new self($el);
	}

	public function render(): void
	{
		echo $this->html;
	}

}
