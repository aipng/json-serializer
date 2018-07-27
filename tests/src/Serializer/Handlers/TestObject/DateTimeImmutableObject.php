<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers\TestObject;

use JMS\Serializer\Annotation as Serializer;

final class DateTimeImmutableObject
{

	/**
	 * @var \DateTimeImmutable|null
	 *
	 * @Serializer\Type("DateTimeImmutable")
	 */
	private $dateTime;


	public function __construct(?\DateTimeImmutable $dateTime = null)
	{
		$this->dateTime = $dateTime;
	}


	public function getDateTime(): ?\DateTimeImmutable
	{
		return $this->dateTime;
	}

}
