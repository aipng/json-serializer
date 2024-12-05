<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Validator;

use AipNg\JsonSerializer\ValidationException;
use AipNg\JsonSerializer\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class SymfonyValidator implements Validator
{

	public function __construct(private ValidatorInterface $validator)
	{
	}


	/**
	 * @throws \AipNg\JsonSerializer\ValidationException
	 */
	public function validate(mixed $value): void
	{
		$violations = $this->validator->validate($value);

		if (count($violations) > 0) {
			$fields = [];

			foreach ($violations as $violation) {
				$fields[$violation->getPropertyPath()][] = (string) $violation->getMessage();
			}

			throw ValidationException::withFields($fields);
		}
	}

}
