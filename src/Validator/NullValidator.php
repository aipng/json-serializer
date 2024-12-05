<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Validator;

use AipNg\JsonSerializer\Validator;

final class NullValidator implements Validator
{

	public function validate(mixed $value): void
	{
	}

}
