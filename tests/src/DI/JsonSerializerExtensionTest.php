<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use AipNg\JsonSerializer\DI\JsonSerializerExtension;
use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JsonSerializer;
use AipNg\ValueObjects\Web\Email;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class JsonSerializerExtensionTest extends TestCase
{

	#[DoesNotPerformAssertions]
	public function testShouldCreateSerializerFactory(): void
	{
		$this->createContainer([
			'temporaryDirectory' => vfsStream::setup()->url(),
		]);
	}


	public function testShouldThrowExceptionWhenTemporaryDirectoryNotSet(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer();
	}


	public function testShouldThrowExceptionWhenGivenTemporaryDirectoryNotWritable(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			'temporaryDirectory' => vfsStream::setup()->url() . '/directory-not-exists',
		]);
	}


	public function testShouldRegisterSerializerHandlers(): void
	{
		$container = $this->createContainer([
			'temporaryDirectory' => vfsStream::setup()->url(),
			'serializationHandlers' => [
				EmailHandler::class,

			],
		]);

		/** @var \AipNg\JsonSerializer\JsonSerializer $serializer */
		$serializer = $container->getByType(JsonSerializer::class);

		$email = new Email('example@example.org');

		$this->assertSame('"example@example.org"', $serializer->serialize($email));
	}


	public function testShouldThrowExceptionOnInvalidSerializerHandler(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			'temporaryDirectory' => vfsStream::setup()->url(),
			'serializationHandlers' => [
				'\SomeNonExistingHandler',
			],
		]);
	}


	/**
	 * @param mixed[] $config
	 */
	private function createContainer(array $config = []): Container
	{
		$loader = new ContainerLoader(vfsStream::setup()->url(), true);

		$class = $loader->load(
			function (Compiler $compiler) use ($config): void {
				$compiler->addConfig([
					'apiSerializer' => $config,
				]);

				$compiler->addExtension('apiSerializer', new JsonSerializerExtension);
			},
			mt_rand(1, 10000),
		);

		/** @var \Nette\DI\Container */
		return new $class;
	}

}
