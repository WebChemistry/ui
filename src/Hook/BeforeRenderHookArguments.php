<?php declare(strict_types = 1);

namespace WebChemistry\UI\Hook;

use WebChemistry\UI\Template\TemplateOptions;

final class BeforeRenderHookArguments
{

	public function __construct(
		public readonly TemplateOptions $options,
		public readonly ?int $counter,
	)
	{
	}

}
