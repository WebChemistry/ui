<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use LogicException;
use Nette\Utils\Arrays;
use Stringable;

/**
 * @template T of object
 * @implements MultiplierSerializer<T>
 */
final class DoctrineMultiplierSerializer implements MultiplierSerializer
{

	private ?Base64MultiplierSerializer $base64;

	/** @var ClassMetadata<T> */
	private ClassMetadata $classMetadata;

	private NativeMultiplierSerializer $nativeSerializer;

	/**
	 * @param class-string<T> $className
	 */
	public function __construct(
		private string $className,
		private EntityManagerInterface $em,
	)
	{
		$this->classMetadata = $this->em->getClassMetadata($this->className); // @phpstan-ignore-line
		$this->nativeSerializer = new NativeMultiplierSerializer();
	}

	/**
	 * @param T $value
	 * @return string
	 */
	public function serialize(mixed $value): string
	{
		$ids = $this->classMetadata->getIdentifierValues($value);

		if (count($ids) === 1) {
			return $this->nativeSerializer->serialize((string) current($ids));
		}

		if (Arrays::some($ids, fn (int|string|Stringable $value) => str_contains((string) $value, '_'))) {
			throw new LogicException('Compound identifier value cannot contain "_" character.');
		}

		return $this->nativeSerializer->serialize(implode('_', $ids));
	}

	/**
	 * @return T|null
	 */
	public function unserialize(string $id): ?object
	{
		$id = $this->nativeSerializer->unserialize($id);

		if ($this->classMetadata->isIdentifierComposite) {
			$id = array_combine(
				$this->classMetadata->getIdentifier(),
				explode('_', $id),
			);
		}

		return $this->em->find($this->className, $id);
	}

}
