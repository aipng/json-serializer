<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

final class JsonSerializerConfig
{

	public ?string $temporaryDirectory = null;

	public bool $productionMode = false;

	/** @var string[] */
	public array $serializationHandlers = [];

}
