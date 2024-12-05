<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

interface Validator
{

	/**
	 * \AipNg\JsonSerializer\ValidationException
	 */
	public function validate(mixed $value): void;

}
