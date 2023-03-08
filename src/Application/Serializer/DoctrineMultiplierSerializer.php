<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use LogicException;

/**
 * @template T of object
 * @implements MultiplierSerializer<T>
 */
final class DoctrineMultiplierSerializer implements MultiplierSerializer
{

	private ?Base64MultiplierSerializer $base64;

	/** @var ClassMetadata<T> */
	private ClassMetadata $classMetadata;

	/**
	 * @param class-string<T> $className
	 */
	public function __construct(
		private string $className,
		private EntityManagerInterface $em,
		bool $base64 = false,
	)
	{
		$this->classMetadata = $this->em->getClassMetadata($this->className); // @phpstan-ignore-line
		$this->base64 = $base64 ? new Base64MultiplierSerializer() : null;

		if (!$base64 && count($this->classMetadata->getIdentifier()) !== 1) {
			throw new LogicException('Entity with more than one identifier must have base64 enabled.');
		}
	}

	/**
	 * @param T $value
	 * @return string
	 */
	public function serialize(mixed $value): string
	{
		$base64 = $this->base64;

		if (!$base64) {
			$values = $this->classMetadata->getIdentifierValues($value);

			if (count($values) !== 1) {
				throw new LogicException('Entity with more than one identifier must have base64 enabled.');
			}

			$value = current($values);

			if (!is_string($value) && !is_int($value)) {
				throw new LogicException('Entity must have string or int identifier, use base64.');
			}

			return (string) $value;
		} else {
			return $base64->serialize($this->classMetadata->getIdentifierValues($value));

		}
	}

	/**
	 * @return T|null
	 */
	public function unserialize(string $id): ?object
	{
		$base64 = $this->base64;

		if (!$base64) {
			return $this->em->find($this->className, $id);

		} else {
			$id = $base64->unserialize($id);

			return $this->em->find($this->className, array_combine(
				$this->classMetadata->getIdentifier(),
				(array) $id,
			));

		}
	}

}
