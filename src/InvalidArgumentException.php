<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

final class InvalidArgumentException extends \InvalidArgumentException
{

	/** @var array<string, string[]> */
	private array $context = [];


	public function withError(string $field, string $message): self
	{
		$this->context[$field][] = $message;

		return $this;
	}


	/**
	 * @return array<string, string[]>
	 */
	public function getContext(): array
	{
		return $this->context;
	}

}
