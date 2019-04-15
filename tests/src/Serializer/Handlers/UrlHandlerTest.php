<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\Serializer\Handlers\UrlHandler;
use AipNg\JsonSerializer\Serializer\JsonSerializerInterface;
use AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\UrlObject;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\TestCase;

final class UrlHandlerTest extends TestCase
{

	public function testSerializeUrlToJson(): void
	{
		$url = $this->createUrl();
		$expectedResult = sprintf(
			'"%s"',
			addcslashes($url->getValue(), '/')
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
			addcslashes($url->getValue(), '/')
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
			addcslashes($url->getValue(), '/')
		);

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\UrlObject $object */
		$object = $this->createSerializer()->deserialize($json, UrlObject::class);

		$this->assertEquals($url, $object->getUrl());
	}


	public function testDeserializeEmptyUrlPropertyFromJson(): void
	{
		$json = '{"url":null}';

		/** @var \AipNg\JsonSerializerTests\Serializer\Handlers\TestObject\UrlObject $object */
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
		$serializer = new JmsJsonSerializerAdapter;
		$serializer->addSubscribingHandler(new UrlHandler);

		return $serializer;
	}


	private function createUrl(): Url
	{
		return new Url('https://www.example.org');
	}

}
