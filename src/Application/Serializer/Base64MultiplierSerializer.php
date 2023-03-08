<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

use InvalidArgumentException;
use LogicException;
use Stringable;

/**
 * @implements MultiplierSerializer<mixed>
 */
final class Base64MultiplierSerializer implements MultiplierSerializer
{

	public function serialize(mixed $value): string
	{

		if (is_scalar($value) || $value instanceof Stringable) {
			$value = (string) $value;
			$code = '0';

		} else if (is_array($value)) {
			foreach ($value as $val) {
				if (str_contains($val, '-')) {
					throw new LogicException(sprintf('Component name cannot contain -, %s given,', $val));
				}
			}

			$value = implode('-', $value);
			$code = '1';

		} else {
			throw new InvalidArgumentException(sprintf('Invalid value given %s', get_debug_type($value)));

		}

		return $code . rtrim(strtr(base64_encode($value), '+/', '._'), '=');
	}

	/**
	 * @param string $id
	 * @return string|mixed[]
	 */
	public function unserialize(string $id): mixed
	{
		$code = substr($id, 0, 1);
		$id = substr($id, 1);

		$decode = base64_decode(strtr($id, '-_', '+/'), true);

		if ($decode === false) {
			throw new LogicException('Base 64 decoding failed.');
		}

		if ($code === '0') {
			return $decode;

		} else {
			return explode('-', $decode);

		}
	}

}
