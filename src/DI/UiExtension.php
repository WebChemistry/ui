<?php declare(strict_types = 1);

namespace WebChemistry\UI\DI;

use Doctrine\ORM\EntityManagerInterface;
use Nette\DI\CompilerExtension;
use WebChemistry\UI\Application\DoctrineMultiplierFactory;

final class UiExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		if (interface_exists(EntityManagerInterface::class)) {
			$builder->addDefinition($this->prefix('multiplier'))
				->setFactory(DoctrineMultiplierFactory::class);
		}
	}

}
