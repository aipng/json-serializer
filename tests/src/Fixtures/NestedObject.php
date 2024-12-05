<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Fixtures;

final class NestedObject
{

	public function __construct(
		public SimpleObject $object,
	)
	{
	}

}
