<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Handlers;

use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\JsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithEmail;
use AipNg\ValueObjects\Web\Email;
use PHPUnit\Framework\TestCase;

final class EmailHandlerTest extends TestCase
{

	public function testShouldSerializeEmailProperty(): void
	{
		$email = Email::from('example@example.org');

		$json = $this->createSerializer()->serialize(new ObjectWithEmail($email));

		$this->assertSame(
			'{"email":"example@example.org"}',
			$json,
		);
	}


	public function testShouldSerializeEmptyEmail(): void
	{
		$json = $this->createSerializer()->serialize(new ObjectWithEmail);

		$this->assertSame(
			'{"email":null}',
			$json,
		);
	}


	public function testShouldDeserializeEmail(): void
	{
		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithEmail $object */
		$object = $this->createSerializer()->deserialize(
			'{"email":"example@example.org"}',
			ObjectWithEmail::class,
		);

		$this->assertNotNull($object->getEmail());
		$this->assertSame(
			'example@example.org',
			$object->getEmail()->getValue(),
		);
	}


	public function testShouldDeserializeEmptyEmail(): void
	{
		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithEmail $object */
		$object = $this->createSerializer()->deserialize(
			'{"email":null}',
			ObjectWithEmail::class,
		);

		$this->assertNull($object->getEmail());
	}


	private function createSerializer(): JsonSerializer
	{
		$serializer = new JmsJsonSerializer($this->createMock(Validator::class));

		$serializer->addSubscribingHandler(new EmailHandler);

		return $serializer;
	}

}
