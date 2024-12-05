<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;

final class FakeContextualValidatorInterface implements ContextualValidatorInterface
{

	public function atPath(string $path): static
	{
		return $this;
	}


	public function validate(
		mixed $value,
		array | Constraint | null $constraints = null,
		array | GroupSequence | string | null $groups = null,
	): static
	{
		return $this;
	}


	public function validateProperty(
		object $object,
		string $propertyName,
		array | GroupSequence | string | null $groups = null,
	): static
	{
		return $this;
	}


	public function validatePropertyValue(
		object | string $objectOrClass,
		string $propertyName,
		mixed $value,
		array | GroupSequence | string | null $groups = null,
	): static
	{
		return $this;
	}


	public function getViolations(): ConstraintViolationListInterface
	{
		return new ConstraintViolationList;
	}

}
