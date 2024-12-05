<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FakeValidator implements ValidatorInterface
{

	private ConstraintViolationListInterface $constraintViolationList;


	public function __construct()
	{
		$this->constraintViolationList = new ConstraintViolationList;
	}


	public function validate(
		mixed $value,
		array | Constraint | null $constraints = null,
		array | GroupSequence | string | null $groups = null,
	): ConstraintViolationListInterface
	{
		return $this->constraintViolationList;
	}


	public function validateProperty(
		object $object,
		string $propertyName,
		array | GroupSequence | string | null $groups = null,
	): ConstraintViolationListInterface
	{
		return $this->constraintViolationList;
	}


	public function validatePropertyValue(
		object | string $objectOrClass,
		string $propertyName,
		mixed $value,
		array | GroupSequence | string | null $groups = null,
	): ConstraintViolationListInterface
	{
		return $this->constraintViolationList;
	}


	public function startContext(): ContextualValidatorInterface
	{
		return new FakeContextualValidatorInterface;
	}


	public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
	{
		return new FakeContextualValidatorInterface;
	}


	public function getMetadataFor(mixed $value): MetadataInterface
	{
		return new FakeMetadataInterface;
	}


	public function hasMetadataFor(mixed $value): bool
	{
		return true;
	}

}
