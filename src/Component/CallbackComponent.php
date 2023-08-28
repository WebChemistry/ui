<?php declare(strict_types = 1);

namespace WebChemistry\UI\Component;

use Nette\Application\UI\Control;

final class CallbackComponent extends Control
{

	/** @var callable(CallbackComponent): void */
	private $callback;

	/**
	 * @param callable(CallbackComponent): void $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function render(): void
	{
		($this->callback)($this);
	}

}
