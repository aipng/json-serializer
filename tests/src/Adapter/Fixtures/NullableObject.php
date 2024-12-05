<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Adapter\Fixtures;

final readonly class NullableObject
{

	public function __construct(
		public ?int $id = null,
		public ?string $name = null,
		public ?\DateTimeImmutable $date = null,
	)
	{
	}

}
