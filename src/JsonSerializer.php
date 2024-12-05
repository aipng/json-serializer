<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

interface JsonSerializer
{

	public function serialize(mixed $data): string;


	/**
	 * @throws \AipNg\JsonSerializer\ValidationException
	 */
	public function deserialize(string $json, string $type): mixed;

}
