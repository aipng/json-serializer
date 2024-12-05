<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration;

use AipNg\JsonSerializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\Handlers\UrlHandler;
use AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithEmailAndUrl;
use AipNg\ValueObjects\Web\Email;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\TestCase;

class AdapterHandlerTest extends TestCase
{

	private JmsJsonSerializerAdapter $serializer;


	protected function setUp(): void
	{
		$this->serializer = new JmsJsonSerializerAdapter;
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
