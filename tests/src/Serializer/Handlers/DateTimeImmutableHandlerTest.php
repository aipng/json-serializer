<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\Serializer\Handlers\DateTimeImmutableHandler;
use AipNg\JsonSerializer\Serializer\JsonSerializerInterface;
use AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\DateTimeImmutableObject;
use PHPUnit\Framework\TestCase;

final class DateTimeImmutableHandlerTest extends TestCase
{

	public function testSerializeDateTimeImmutableToJson(): void
	{
		$dateTimeImmutable = $this->createDateTimeImmutable();
		$expectedResult = sprintf(
			'"%s"',
			$dateTimeImmutable->format(\DateTime::ATOM),
		);

		$json = $this->getSerializer()->serialize($dateTimeImmutable);

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeDateTimeImmutablePropertyToJson(): void
	{
		$dateTimeImmutable = $this->createDateTimeImmutable();
		$object = new DateTimeImmutableObject($dateTimeImmutable);
		$expectedResult = sprintf(
			'{"dateTime":"%s"}',
			$dateTimeImmutable->format(\DateTime::ATOM),
		);

		$json = $this->getSerializer()->serialize($object);

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeEmptyDateTimeImmutablePropertyToJson(): void
	{
		$object = new DateTimeImmutableObject;
		$expectedResult = '{"dateTime":null}';

		$json = $this->getSerializer()->serialize($object);

		$this->assertSame($expectedResult, $json);
	}


	public function testDeserializeDateTimeImmutableFromJson(): void
	{
		$dateTimeImmutable = $this->createDateTimeImmutable();
		$json = sprintf(
			'{"dateTime":"%s"}',
			$dateTimeImmutable->format(\DateTime::ATOM),
		);

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\DateTimeImmutableObject $object */
		$object = $this->getSerializer()->deserialize($json, DateTimeImmutableObject::class);

		$this->assertInstanceOf(\DateTimeImmutable::class, $object->getDateTime());
		$this->assertEquals($dateTimeImmutable, $object->getDateTime());
	}


	public function testDeserializeEmptyPropertyFromJson(): void
	{
		$json = '{"dateTime":null}';

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\DateTimeImmutableObject $object */
		$object = $this->getSerializer()->deserialize($json, DateTimeImmutableObject::class);

		$this->assertNull($object->getDateTime());
	}


	public function testThrowExceptionOnInvalidInputOnDeserialization(): void
	{
		$json = '{"dateTime":"not-a-date"}';

		$this->expectException(InvalidArgumentException::class);

		$this->getSerializer()->deserialize($json, DateTimeImmutableObject::class);
	}


	protected function getSerializer(): JsonSerializerInterface
	{
		$serializer = new JmsJsonSerializerAdapter;
		$serializer->addSubscribingHandler(new DateTimeImmutableHandler);

		return $serializer;
	}


	private function createDateTimeImmutable(): \DateTimeImmutable
	{
		return new \DateTimeImmutable('2018-01-21 01:02:03', new \DateTimeZone('Europe/Prague'));
	}

}
