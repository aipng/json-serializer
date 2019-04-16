<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

final class JsonSerializerConfig
{

	/** @var string|null */
	public $temporaryDirectory;

	/** @var bool */
	public $productionMode = false;

	/** @var string[] */
	public $serializationHandlers = [];

}
