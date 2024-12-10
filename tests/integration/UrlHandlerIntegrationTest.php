<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration;

use AipNg\JsonSerializer\Handlers\UrlHandler;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\JsonSerializer;
use AipNg\JsonSerializer\Validator;
use AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithUrl;
use AipNg\ValueObjects\Web\Url;
use PHPUnit\Framework\TestCase;

final class UrlHandlerIntegrationTest extends TestCase
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

		/** @var \AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithUrl $object */
		$object = $this->createSerializer()->deserialize(
			'{"url":"https:\/\/www.example.org"}',
			ObjectWithUrl::class,
		);

		$this->assertEquals($url, $object->getUrl());
	}


	public function testShouldDeserializeEmptyUrl(): void
	{
		/** @var \AipNg\JsonSerializerTests\Integration\Fixtures\ObjectWithUrl $object */
		$object = $this->createSerializer()->deserialize(
			'{"url":null}',
			ObjectWithUrl::class,
		);

		$this->assertNull($object->getUrl());
	}


	public function testShouldAddExceptionContext(): void
	{
		try {
			$this->createSerializer()->deserialize(
				'{"url":"x"}',
				ObjectWithUrl::class,
			);
		} catch (InvalidArgumentException $e) {
			$this->assertSame(404, $e->getCode());
			$this->assertSame('Invalid JSON input!', $e->getMessage());
			$this->assertArrayHasKey('url', $e->getContext());
			$this->assertCount(1, $e->getContext()['url']);
		}
	}


	private function createSerializer(): JsonSerializer
	{
		$serializer = new JmsJsonSerializer($this->createMock(Validator::class));

		$serializer->addSubscribingHandler(new UrlHandler);

		return $serializer;
	}

}
