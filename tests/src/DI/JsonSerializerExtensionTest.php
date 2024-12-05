<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

use AipNg\JsonSerializer\DI\JsonSerializerConfig;
use AipNg\JsonSerializer\DI\JsonSerializerExtension;
use AipNg\JsonSerializer\Handlers\EmailHandler;
use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JsonSerializerInterface;
use AipNg\ValueObjects\Web\Email;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class JsonSerializerExtensionTest extends TestCase
{

	private const string EXTENSION_NAME = 'apiSerializer';


	public function testDIExtensionCreatesSerializerFactory(): void
	{
		$config = new JsonSerializerConfig;
		$config->temporaryDirectory = $this->getTemporaryDirectory();

		$container = $this->createContainer((array) $config);

		$this->assertInstanceOf(JsonSerializerInterface::class, $container->getByType(JsonSerializerInterface::class));
	}


	public function testThrowExceptionWhenTemporaryDirectoryNotSet(): void
	{
		$config = new JsonSerializerConfig;

		$this->expectException(InvalidArgumentException::class);

		$this->createContainer((array) $config);
	}


	public function testThrowExceptionWhenGivenTemporaryDirectoryIsNotWritable(): void
	{
		$config = new JsonSerializerConfig;
		$config->temporaryDirectory = $this->getTemporaryDirectory() . '/directory-not-exists';

		$this->expectException(InvalidArgumentException::class);

		$this->createContainer((array) $config);
	}


	public function testSerializerFactoryRegistersSerializerHandlers(): void
	{
		$config = new JsonSerializerConfig;
		$config->temporaryDirectory = $this->getTemporaryDirectory();
		$config->serializationHandlers = [
			EmailHandler::class,
		];

		$container = $this->createContainer((array) $config);

		/** @var \AipNg\JsonSerializer\JsonSerializerInterface $serializer */
		$serializer = $container->getByType(JsonSerializerInterface::class);

		$email = new Email('example@example.org');

		$this->assertSame('"example@example.org"', $serializer->serialize($email));
	}


	public function testThrowExceptionOnInvalidSerializerHandler(): void
	{
		$config = new JsonSerializerConfig;
		$config->temporaryDirectory = $this->getTemporaryDirectory();
		$config->serializationHandlers = [
			'\SomeNonExistingHandler',
		];

		$this->expectException(InvalidArgumentException::class);

		$this->createContainer((array) $config);
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
