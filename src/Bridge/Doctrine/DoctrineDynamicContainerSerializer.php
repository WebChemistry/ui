<?php declare(strict_types = 1);

namespace WebChemistry\UI\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use WebChemistry\UI\Container\DynamicContainerSerializer;

/**
 * @template TValue of object
 * @implements DynamicContainerSerializer<TValue>
 */
final class DoctrineDynamicContainerSerializer implements DynamicContainerSerializer
{

	private const ListDelimiter = '__d__';

	/** @var string[] */
	private array $ids;

	/**
	 * @param class-string<TValue> $entityClass
	 */
	public function __construct(
		private string $entityClass,
		private EntityManagerInterface $em,
		private bool $convertDashToUnderscore = false,
	)
	{
	}

	/**
	 * @return TValue|null
	 */
	public function unserialize(string $name): mixed
	{
		return $this->em->find($this->entityClass, $this->convertNameToIds($name));
	}

	public function serialize(mixed $value): string
	{
		if (!$value instanceof $this->entityClass) {
			throw new InvalidArgumentException(
				sprintf('Value must be instance of %s, %s given.', $this->entityClass, get_debug_type($value)),
			);
		}

		$metadata = $this->em->getClassMetadata($this->entityClass);
		$idValues = $metadata->getIdentifierValues($value);

		$values = [];
		foreach ($this->getIdNames() as $id) {
			if (!is_scalar($idValues[$id])) {
				throw new InvalidArgumentException(sprintf('Identifier %s is not scalar.', $id));
			}

			$idValue = (string) $idValues[$id];

			if ($this->convertDashToUnderscore) {
				$idValue = str_replace('-', '_', $idValue);
			} else if (str_contains($idValue, '-')) {
				throw new InvalidArgumentException(
					sprintf('Value %s contains dash, but convertDashToUnderscore is set to false.', $idValue),
				);
			}

			$values[] = $idValue;
		}

		return implode(self::ListDelimiter, $values);
	}

	/**
	 * @return array<string, string>
	 */
	public function convertNameToIds(string $name): array
	{
		$ids = $this->getIdNames();

		if (count($ids) === 1) {
			return [$ids[0] => $name];
		}

		$values = explode(self::ListDelimiter, $name);

		if (count($ids) !== count($values)) {
			throw new InvalidArgumentException(
				sprintf('Value %s does not contain %d values.', $name, count($ids)),
			);
		}

		return array_combine($ids, $values);
	}

	/**
	 * @return string[]
	 */
	private function getIdNames(): array
	{
		if (!isset($this->ids)) {
			$this->ids = $this->em->getClassMetadata($this->entityClass)->getIdentifierFieldNames();

			sort($this->ids, SORT_STRING);
		}

		return $this->ids;
	}

}
