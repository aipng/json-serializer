<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Adapter\Fixtures;

use JMS\Serializer\Annotation\Type;

final class SimpleObject
{

	public function __construct(
		public int $id,
		public string $name,
		#[Type(name: 'DateTimeImmutable<"Y-m-d">')]
		public \DateTimeImmutable $date,
		public bool $active,
		public MyEnum $myEnum,
	)
	{
	}

}
