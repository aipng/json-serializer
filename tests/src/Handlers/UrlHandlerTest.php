<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Handlers;

use AipNg\JsonSerializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\Handlers\UrlHandler;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JsonSerializerInterface;
use AipNg\JsonSerializerTests\Handlers\TestObject\UrlObject;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UrlHandlerTest extends TestCase
{

	public function testSerializeUrlToJson(): void
	{
		$url = $this->createUrl();
		$expectedResult = sprintf(
			'"%s"',
			addcslashes($url->getValue(), '/'),
		);

		$json = $this->createSerializer()->serialize($url);

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeUrlPropertyToJson(): void
	{
		$url = $this->createUrl();
		$object = new UrlObject($url);
		$expectedResult = sprintf(
			'{"url":"%s"}',
			addcslashes($url->getValue(), '/'),
		);

		$json = $this->createSerializer()->serialize($object);

		$this->assertSame($expectedResult, $json);
	}


	public function testSerializeEmptyPropertyToJson(): void
	{
		$expectedResult = '{"url":null}';

		$json = $this->createSerializer()->serialize(new UrlObject);

		$this->assertSame($expectedResult, $json);
	}


	public function testDeserializeUrlPropertyFromJson(): void
	{
		$url = $this->createUrl();
		$json = sprintf(
			'{"url":"%s"}',
			addcslashes($url->getValue(), '/'),
		);

		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\UrlObject $object */
		$object = $this->createSerializer()->deserialize($json, UrlObject::class);

		$this->assertEquals($url, $object->getUrl());
	}


	public function testDeserializeEmptyUrlPropertyFromJson(): void
	{
		$json = '{"url":null}';

		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\UrlObject $object */
		$object = $this->createSerializer()->deserialize($json, UrlObject::class);

		$this->assertNull($object->getUrl());
	}


	public function testThrowExceptionOnInvalidOnDeserialization(): void
	{
		$json = '{"url":"not-an-url"}';

		$this->expectException(InvalidArgumentException::class);

		$this->createSerializer()->deserialize($json, UrlObject::class);
	}


	private function createSerializer(): JsonSerializerInterface
	{
		$serializer = new JmsJsonSerializerAdapter($this->createMock(ValidatorInterface::class));
		$serializer->addSubscribingHandler(new UrlHandler);

		return $serializer;
	}


	private function createUrl(): Url
	{
		return new Url('https://www.example.org');
	}

}
