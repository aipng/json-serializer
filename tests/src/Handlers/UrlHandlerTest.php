<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Handlers;

use AipNg\JsonSerializer\Handlers\UrlHandler;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\JsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithUrl;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\TestCase;

final class UrlHandlerTest extends TestCase
{

	public function testShouldSerializeUrl(): void
	{
		$object = new ObjectWithUrl(
			Url::from('https://www.example.org'),
		);

		$json = $this->createSerializer()->serialize($object);

		$this->assertSame(
			'{"url":"https:\/\/www.example.org"}',
			$json,
		);
	}


	public function testShouldSerializeEmptyUrl(): void
	{
		$json = $this->createSerializer()->serialize(new ObjectWithUrl);

		$this->assertSame(
			'{"url":null}',
			$json,
		);
	}


	public function testShouldDeserializeUrlProperty(): void
	{
		$url = Url::from('https://www.example.org');

		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithUrl $object */
		$object = $this->createSerializer()->deserialize(
			'{"url":"https:\/\/www.example.org"}',
			ObjectWithUrl::class,
		);

		$this->assertEquals($url, $object->getUrl());
	}


	public function testShouldDeserializeEmptyUrl(): void
	{
		/** @var \AipNg\JsonSerializerTests\Handlers\TestObject\ObjectWithUrl $object */
		$object = $this->createSerializer()->deserialize(
			'{"url":null}',
			ObjectWithUrl::class,
		);

		$this->assertNull($object->getUrl());
	}


	private function createSerializer(): JsonSerializer
	{
		$serializer = new JmsJsonSerializer($this->createMock(Validator::class));

		$serializer->addSubscribingHandler(new UrlHandler);

		return $serializer;
	}

}
