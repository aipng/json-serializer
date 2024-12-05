<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration;

use AipNg\JsonSerializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\ValidationException;
use AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithConstraints;
use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class JmsJsonSerializerAdapterValidationTest extends TestCase
{

	private JmsJsonSerializerAdapter $serializer;


	protected function setUp(): void
	{
		$this->serializer = new JmsJsonSerializerAdapter(
			Validation::createValidatorBuilder()
				->enableAttributeMapping()
				->getValidator(),
		);
	}


	public function testShoutValidateObjectAfterDeserialization(): void
	{
		$validJson = Json::encode([
			'name' => 'john doe',
			'email' => 'john.doe@example.com',
			'age' => 30,
		]);

		$deserializedObject = $this->serializer->deserialize($validJson, ObjectWithConstraints::class);
		$this->assertInstanceOf(ObjectWithConstraints::class, $deserializedObject);
	}


	public function testItThrowsExceptionOnInvalidObject(): void
	{
		try {
			$invalidJson = Json::encode([
				'name' => '',
				'email' => 'john',
				'age' => 2,
			]);

			$this->serializer->deserialize($invalidJson, ObjectWithConstraints::class);
		} catch (ValidationException $e) {
			$fields = $e->getFields();

			$this->assertCount(3, $fields, 'Should have three invalid fields');

			$this->assertArrayHasKey('name', $fields);
			$this->assertArrayHasKey('email', $fields);
			$this->assertArrayHasKey('age', $fields);
		}
	}

}
