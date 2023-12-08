<?php declare(strict_types = 1);

namespace WebChemistry\UI\Template;

use Latte\Runtime\Template;
use WebChemistry\UI\Hook\AfterRenderHookArguments;
use WebChemistry\UI\Hook\BeforeRenderHookArguments;
use WebChemistry\UI\Renderable\Renderable;

final class TemplateOptions
{

	private bool $startTag = false;

	private bool $endTag = false;

	private int $counter = -1;

	private Template $template;

	/**
	 * @param array<callable(BeforeRenderHookArguments): void> $beforeRenderHooks
	 * @param array<callable(AfterRenderHookArguments): void> $afterRenderHooks
	 */
	public function __construct(
		private array $beforeRenderHooks,
		private array $afterRenderHooks,
	)
	{
	}

	public function initialize(Template $template): void
	{
		$this->template = $template;
	}

	public function finalize(): void
	{
		$this->beforeRender(null);
		$this->afterRender(null);
	}

	public function withStartTag(): self
	{
		$clone = clone $this;
		$clone->startTag = true;
		$clone->beforeRenderHooks = [];
		$clone->afterRenderHooks = [];

		return $clone;
	}

	public function withEndTag(): self
	{
		$clone = clone $this;
		$clone->endTag = true;
		$clone->beforeRenderHooks = [];
		$clone->afterRenderHooks = [];

		return $clone;
	}

	public function isStartTag(): bool
	{
		return $this->startTag;
	}

	public function isEndTag(): bool
	{
		return $this->endTag;
	}

	/**
	 * @template TReturn
	 * @param callable(): TReturn $callback
	 * @return TReturn
	 */
	public function applyHooks(callable $callback, bool $core = false): mixed
	{
		if (!$core) {
			return $callback();
		}

		$this->counter++;

		$this->beforeRender($this->counter);

		$return = $callback();

		$this->afterRender($this->counter);

		return $return;
	}

	private function beforeRender(?int $counter): void
	{
		$args = new BeforeRenderHookArguments($this, $counter);

		foreach ($this->beforeRenderHooks as $callback) {
			$callback($args);
		}
	}

	private function afterRender(?int $counter): void
	{
		$args = new AfterRenderHookArguments($this, $counter);

		foreach ($this->afterRenderHooks as $callback) {
			$callback($args);
		}
	}

	public function render(Renderable $renderable): void
	{
		$renderable->render($this->template, $this);
	}

}
