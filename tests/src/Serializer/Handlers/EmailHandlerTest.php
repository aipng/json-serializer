<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\Serializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\Serializer\JsonSerializerInterface;
use AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\EmailObject;
use AipNg\ValueObjects\Web\Email;
use PHPUnit\Framework\TestCase;

final class EmailHandlerTest extends TestCase
{

	public function testSerializeEmailToJson(): void
	{
		$email = $this->createEmail();
		$expectedResult = sprintf(
			'"%s"',
			$email->getValue()
		);

		$json = $this->createSerializer()->serialize($email);

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeEmailPropertyToJson(): void
	{
		$email = $this->createEmail();
		$expectedResult = sprintf(
			'{"email":"%s"}',
			$email->getValue()
		);

		$json = $this->createSerializer()->serialize(new EmailObject($email));

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeEmptyEmailPropertyToJson(): void
	{
		$expectedResult = '{"email":null}';

		$json = $this->createSerializer()->serialize(new EmailObject);

		$this->assertSame($expectedResult, $json);
	}


	public function testDeserializeEmailPropertyFromJson(): void
	{
		$email = $this->createEmail();
		$json = sprintf(
			'{"email":"%s"}',
			$email->getValue()
		);

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\EmailObject $object */
		$object = $this->createSerializer()->deserialize($json, EmailObject::class);

		$this->assertNotNull($object->getEmail());
		$this->assertSame($email->getValue(), $object->getEmail()->getValue());
	}


	public function testDeserializeEmptyEmailPropertyFromJson(): void
	{
		$json = '{"email":null}';

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\EmailObject $object */
		$object = $this->createSerializer()->deserialize($json, EmailObject::class);

		$this->assertNull($object->getEmail());
	}


	public function testThrowExceptionOnInvalidInputOnDeserialization(): void
	{
		$json = '{"email":"not-en-email"}';

		$this->expectException(InvalidArgumentException::class);

		$this->createSerializer()->deserialize($json, EmailObject::class);
	}


	private function createSerializer(): JsonSerializerInterface
	{
		$serializer = new JmsJsonSerializerAdapter;
		$serializer->addSubscribingHandler(new EmailHandler);

		return $serializer;
	}


	private function createEmail(): Email
	{
		return new Email('example@example.org');
	}

}
