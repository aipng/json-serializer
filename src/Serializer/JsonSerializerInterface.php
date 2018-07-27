<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer;

interface JsonSerializerInterface
{

	/** @param mixed $data */
	public function serialize($data): string;


	/** @return mixed */
	public function deserialize(string $json, string $type);

}
