<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Utils\Html;
use WebChemistry\UI\Template\TemplateOptions;

final class DecoratedControlToRender implements Renderable
{

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(
		private string $name,
		private Control $control,
		private Html $html,
		private array $arguments = [],
		private ?string $renderMethod = null,
	)
	{
	}

	public function redrawControl(?string $snippet = null, bool $redraw = true): void
	{
		$this->control->redrawControl($snippet, $redraw);
	}

	public function isControlInvalid(): bool
	{
		return $this->control->isControlInvalid();
	}

	public function install(Control $control): void
	{
		$control->addComponent($this->control, $this->name);
	}

	public function render(Template $template, TemplateOptions $options, bool $core = false): void
	{
		$method = sprintf('render%s', ucfirst($this->renderMethod ?? ''));

		if (!method_exists($this->control, $method)) {
			throw new LogicException(sprintf('Control %s does not have method %s.', $this->control::class, $method));
		}

		$this->control->redrawControl(null, false);

		$options->applyHooks(function () use ($method) {
			echo $this->html->startTag();

			$this->control->$method(...$this->arguments);

			echo $this->html->endTag();
		}, $core);
	}

}
