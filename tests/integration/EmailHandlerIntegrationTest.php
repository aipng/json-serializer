<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration;

use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\JsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithEmail;
use AipNg\ValueObjects\Web\Email;
use PHPUnit\Framework\TestCase;

final class EmailHandlerIntegrationTest extends TestCase
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
		/** @var \AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithEmail $object */
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
		/** @var \AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithEmail $object */
		$object = $this->createSerializer()->deserialize(
			'{"email":null}',
			ObjectWithEmail::class,
		);

		$this->assertNull($object->getEmail());
	}


	public function testShouldAddExceptionContext(): void
	{
		try {
			$this->createSerializer()->deserialize(
				'{"email":"x"}',
				ObjectWithEmail::class,
			);
		} catch (InvalidArgumentException $e) {
			$this->assertSame(404, $e->getCode());
			$this->assertSame('Invalid JSON input!', $e->getMessage());
			$this->assertArrayHasKey('email', $e->getContext());
			$this->assertCount(1, $e->getContext()['email']);
		}
	}


	private function createSerializer(): JsonSerializer
	{
		$serializer = new JmsJsonSerializer($this->createMock(Validator::class));

		$serializer->addSubscribingHandler(new EmailHandler);

		return $serializer;
	}

}
