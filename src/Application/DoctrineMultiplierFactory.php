<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Control;
use WebChemistry\UI\Application\Serializer\DoctrineMultiplierSerializer;
use WebChemistry\UI\Application\Serializer\MultiplierSerializer;

final class DoctrineMultiplierFactory
{

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @template T of object
	 * @param class-string<T> $className
	 * @param callable(T): Control $factory
	 * @param iterable<MultiplierCached<T>|T> $cache
	 * @return Multiplier<T>
	 */
	public function create(string $className, callable $factory, iterable $cache = []): Multiplier
	{
		$multiplierFactory = new MultiplierFactory(new DoctrineMultiplierSerializer($className, $this->em));

		return $multiplierFactory->create($factory, $cache);
	}

}
