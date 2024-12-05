<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

final class ValidationException extends \Exception
{

	/** @var array<string, string[]> */
	private array $fields = [];


	/**
	 * @param array<string, string[]> $fields
	 */
	public static function withFields(array $fields): self
	{
		$self = new self;
		$self->fields = $fields;

		return $self;
	}


	/**
	 * @return array<string, string[]>
	 */
	public function getFields(): array
	{
		return $this->fields;
	}

}
