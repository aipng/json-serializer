<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use Symfony\Component\Validator\Mapping\MetadataInterface;

final class FakeMetadataInterface implements MetadataInterface
{

	public function getCascadingStrategy(): int
	{
		return 1;
	}


	public function getTraversalStrategy(): int
	{
		return 1;
	}


	/** @inheritDoc */
	public function getConstraints(): array
	{
		return [];
	}


	/** @inheritDoc */
	public function findConstraints(string $group): array
	{
		return [];
	}

}
