<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Handlers;

use AipNg\JsonSerializer\Handlers\EmailHandler;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\JsonDeserializationVisitor;
use PHPUnit\Framework\TestCase;

final class EmailHandlerTest extends TestCase
{

	public function testShouldThrowExceptionOnNumberInput(): void
	{
		$handler = new EmailHandler;

		$this->expectException(\InvalidArgumentException::class);

		$handler->deserializeFromJson(
			new JsonDeserializationVisitor,
			1,
			[],
			new DeserializationContext,
		);
	}

}
