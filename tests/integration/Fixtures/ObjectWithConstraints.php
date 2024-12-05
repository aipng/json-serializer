<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration\Fixtures;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class ObjectWithConstraints
{

	public function __construct(
		#[NotBlank]
		public string $name,
		#[Email]
		public string $email,
		#[GreaterThan(5)]
		public int $age,
	)
	{
	}

}
