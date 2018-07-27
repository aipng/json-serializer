<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use AipNg\JsonSerializer\DI\JsonSerializerExtension;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\Serializer\JsonSerializerInterface;
use AipNg\JsonSerializerTests\TestCase;
use AipNg\ValueObjects\Web\Email;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use org\bovigo\vfs\vfsStream;

final class JsonSerializerExtensionTest extends TestCase
{

	private const EXTENSION_NAME = 'apiSerializer';


	public function testDIExtensionCreatesSerializerFactory(): void
	{
		$container = $this->createContainer([
			JsonSerializerExtension::CONFIG_TEMPORARY_DIRECTORY => $this->getTemporaryDirectory(),
		]);

		$this->assertInstanceOf(JsonSerializerInterface::class, $container->getByType(JsonSerializerInterface::class));
	}


	public function testThrowExceptionWhenTemporaryDirectoryNotSet(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			JsonSerializerExtension::CONFIG_TEMPORARY_DIRECTORY => null,
		]);
	}


	public function testThrowExceptionWhenGivenTemporaryDirectoryIsNotWritable(): void
	{
		$temporaryDirectory = $this->getTemporaryDirectory() . '/directory-not-exists';

		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			JsonSerializerExtension::CONFIG_TEMPORARY_DIRECTORY => $temporaryDirectory,
		]);
	}


	public function testSerializerFactoryRegistersSerializerHandlers(): void
	{
		$container = $this->createContainer([
			JsonSerializerExtension::CONFIG_TEMPORARY_DIRECTORY => $this->getTemporaryDirectory(),
			JsonSerializerExtension::CONFIG_SERIALIZATION_HANDLERS => [
				EmailHandler::class,
			],
		]);

		/** @var \AipNg\JsonSerializer\Serializer\JsonSerializerInterface $serializer */
		$serializer = $container->getByType(JsonSerializerInterface::class);

		$email = new Email('example@example.org');

		$this->assertSame('"example@example.org"', $serializer->serialize($email));
	}


	public function testThrowExceptionOnInvalidSerializerHandler(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			JsonSerializerExtension::CONFIG_TEMPORARY_DIRECTORY => $this->getTemporaryDirectory(),
			JsonSerializerExtension::CONFIG_SERIALIZATION_HANDLERS => [
				'\SomeNonExistingHandler',
			],
		]);
	}


	/**
	 * @param mixed[] $extensionConfig
	 */
	private function createContainer(array $extensionConfig = []): Container
	{
		$loader = new ContainerLoader($this->getTemporaryDirectory(), true);

		$class = $loader->load(function (Compiler $compiler) use ($extensionConfig): void {
			$compiler->addConfig([
				self::EXTENSION_NAME => $extensionConfig,
			]);

			$compiler->addExtension(self::EXTENSION_NAME, new JsonSerializerExtension);
		}, $this->getGeneratedContainerKey());

		/** @var \Nette\DI\Container $container */
		$container = new $class;

		return $container;
	}


	private function getTemporaryDirectory(): string
	{
		return vfsStream::setup('root')->url();
	}


	private function getGeneratedContainerKey(): int
	{
		return mt_rand(1, 10000);
	}

}
