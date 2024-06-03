<?php declare(strict_types = 1);

namespace WebChemistry\UI\Application\Serializer;

use LogicException;
use Nette\Utils\Strings;

/**
 * @implements MultiplierSerializer<string|int>
 */
final class NativeMultiplierSerializer implements MultiplierSerializer
{

	/**
	 * @param string|int $value
	 * @return string
	 */
	public function serialize(mixed $value): string
	{
		$value = (string) $value;

		if (str_starts_with($value, '_') || !Strings::match($value, '/^[a-zA-Z0-9_]+$/')) {
			$value = '_' . $this->encode($value);
		}

		return $value;
	}

	public function unserialize(string $id): string
	{
		if (str_starts_with($id, '_')) {
			$id = $this->decode(substr($id, 1));
		}

		return $id;
	}

	private function encode(string $value): string
	{
		return rtrim(strtr(base64_encode($value), '+/', '._'), '=');
	}

	private function decode(string $value): string
	{
		$decoded = base64_decode(strtr($value, '._', '+/'), true);

		if ($decoded === false) {
			throw new LogicException('Base64 decoding failed.');
		}

		return $decoded;
	}

}
