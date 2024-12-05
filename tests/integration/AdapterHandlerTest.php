<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration;

use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\Handlers\UrlHandler;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithEmailAndUrl;
use AipNg\ValueObjects\Web\Email;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdapterHandlerTest extends TestCase
{

	private JmsJsonSerializer $serializer;

	private Validator & MockObject $validator;


	protected function setUp(): void
	{
		$this->validator = $this->createMock(Validator::class);
		$this->serializer = new JmsJsonSerializer($this->validator);
		$this->serializer
			->addSubscribingHandler(new EmailHandler)
			->addSubscribingHandler(new UrlHandler);
	}


	public function testShouldSerializeAndDeserializeObjectWithCustomHandlers(): void
	{
		$testObject = new ObjectWithEmailAndUrl(
			Email::from('john.doe@example.com'),
			Url::from('https://www.example.com'),
		);

		$serializedJson = $this->serializer->serialize($testObject);
		$deserializedObject = $this->serializer->deserialize($serializedJson, ObjectWithEmailAndUrl::class);

		$this->assertInstanceOf(ObjectWithEmailAndUrl::class, $deserializedObject);
		$this->assertSame($testObject->email->getValue(), $deserializedObject->email->getValue());
		$this->assertSame($testObject->url->getValue(), $deserializedObject->url->getValue());
	}

}
