<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

interface JsonSerializerInterface
{

	public function serialize(mixed $data): string;


	public function deserialize(string $json, string $type): mixed;

}
