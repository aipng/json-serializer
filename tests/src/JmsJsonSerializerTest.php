<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests;

use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Fixtures\MyEnum;
use AipNg\JsonSerializerTests\Fixtures\NestedObject;
use AipNg\JsonSerializerTests\Fixtures\NullableObject;
use AipNg\JsonSerializerTests\Fixtures\SimpleObject;
use PHPUnit\Framework\TestCase;

final class JmsJsonSerializerTest extends TestCase
{

	private JmsJsonSerializer $serializer;


	protected function setUp(): void
	{
		$this->serializer = new JmsJsonSerializer($this->createMock(Validator::class));
	}


	public function testShouldSerializeAndDeserializeSimpleObject(): void
	{
		$testObject = new SimpleObject(
			0,
			'name',
			new \DateTimeImmutable('2024-09-21'),
			true,
			MyEnum::FOO,
		);

		$serializedJson = $this->serializer->serialize($testObject);
		$deserializedObject = $this->serializer->deserialize($serializedJson, SimpleObject::class);

		$this->assertInstanceOf(SimpleObject::class, $deserializedObject);
		$this->assertSame($testObject->id, $deserializedObject->id);
		$this->assertSame($testObject->name, $deserializedObject->name);
		$this->assertSame($testObject->date->format('Y-m-d'), $deserializedObject->date->format('Y-m-d'));
		$this->assertSame($testObject->active, $deserializedObject->active);
		$this->assertSame($testObject->myEnum->value, $deserializedObject->myEnum->value);
	}


	public function testShouldSerializeAndDeserializeNullables(): void
	{
		$testObject = new NullableObject;

		$serializedJson = $this->serializer->serialize($testObject);
		$deserializedObject = $this->serializer->deserialize($serializedJson, NullableObject::class);

		$this->assertInstanceOf(NullableObject::class, $deserializedObject);
		$this->assertNull($deserializedObject->id);
		$this->assertNull($deserializedObject->name);
		$this->assertNull($deserializedObject->date);
	}


	public function testShouldSerializeAndDeserializeNestedObject(): void
	{
		$testObject = new NestedObject(
			new SimpleObject(
				0,
				'name',
				new \DateTimeImmutable('2024-09-21'),
				true,
				MyEnum::FOO,
			),
		);

		$serializedJson = $this->serializer->serialize($testObject);
		$deserializedObject = $this->serializer->deserialize($serializedJson, NestedObject::class);

		$this->assertInstanceOf(NestedObject::class, $deserializedObject);
		$this->assertSame($testObject->object->id, $deserializedObject->object->id);
		$this->assertSame($testObject->object->name, $deserializedObject->object->name);
		$this->assertSame($testObject->object->date->format('Y-m-d'), $deserializedObject->object->date->format('Y-m-d'));
		$this->assertSame($testObject->object->active, $deserializedObject->object->active);
		$this->assertSame($testObject->object->myEnum->value, $deserializedObject->object->myEnum->value);
	}

}
