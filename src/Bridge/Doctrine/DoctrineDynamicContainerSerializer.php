<?php declare(strict_types = 1);

namespace WebChemistry\UI\Bridge\Doctrine;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use WebChemistry\UI\Container\DynamicContainerSerializer;

/**
 * @template TValue of object
 * @implements DynamicContainerSerializer<TValue>
 */
final class DoctrineDynamicContainerSerializer implements DynamicContainerSerializer
{

	/** @var string[] */
	private array $ids;

	/**
	 * @param class-string<TValue> $entityClass
	 */
	public function __construct(
		private string $entityClass,
		private EntityManagerInterface $em,
		private bool $convertDashToUnderscore = false,
		private string $delimiter = '__d__',
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

		return implode($this->delimiter, $this->getIdentifiers($value));
	}

	/**
	 * @return string[]
	 */
	private function getIdentifiers(object $entity): array
	{
		$metadata = $this->em->getClassMetadata($className = ClassUtils::getClass($entity));
		$idValues = $metadata->getIdentifierValues($entity);

		$values = [];
		foreach ($this->getIdNames($className) as $id) {
			$value = $idValues[$id];
			if (!is_scalar($value)) {
				if (is_object($value)) {
					$values = array_merge($values, $this->getIdentifiers($value));

					continue;
				}

				throw new InvalidArgumentException(sprintf('Identifier %s is not scalar, %s given.', $id, get_debug_type($value)));
			}

			$value = (string) $value;

			if ($this->convertDashToUnderscore) {
				$value = str_replace('-', '_', $value);
			} else if (str_contains($value, '-')) {
				throw new InvalidArgumentException(
					sprintf('Value %s contains dash, but convertDashToUnderscore is set to false.', $value),
				);
			}

			$values[] = $value;
		}

		return $values;
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

		$values = explode($this->delimiter, $name);

		if (count($ids) !== count($values)) {
			throw new InvalidArgumentException(
				sprintf('Value %s does not contain %d values.', $name, count($ids)),
			);
		}

		return array_combine($ids, $values);
	}

	/**
	 * @param class-string|null $className
	 * @return string[]
	 */
	private function getIdNames(?string $className = null): array
	{
		if ($className && $className !== $this->entityClass) {
			$ids = $this->em->getClassMetadata($className ?? $this->entityClass)->getIdentifierFieldNames();

			sort($ids, SORT_STRING);

			return $ids;
		}

		if (!isset($this->ids)) {
			$this->ids = $this->em->getClassMetadata($this->entityClass)->getIdentifierFieldNames();

			sort($this->ids, SORT_STRING);
		}

		return $this->ids;
	}

}
