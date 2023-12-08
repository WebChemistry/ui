<?php declare(strict_types = 1);

namespace WebChemistry\UI\Renderable;

use Latte\Runtime\Template;
use LogicException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Renderable as UIRenderable;
use WebChemistry\UI\Template\TemplateOptions;

final class ControlToRender implements Renderable, UIRenderable
{

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(
		private string $name,
		private Control $control,
		private array $arguments = [],
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
		$this->control->redrawControl(null, false);

		if (!method_exists($this->control, 'render')) {
			throw new LogicException(sprintf('Control %s does not have method render.', $this->control::class));
		}

		$options->applyHooks(fn () => $this->control->render(...$this->arguments), $core);
	}

}
