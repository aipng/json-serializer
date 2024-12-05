<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\DI;

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


	public function testShouldCreateSerializerFactory(): void
	{
		$container = $this->createContainer([
			'temporaryDirectory' => $this->getTemporaryDirectory(),
		]);

		$this->assertInstanceOf(JsonSerializerInterface::class, $container->getByType(JsonSerializerInterface::class));
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
			'temporaryDirectory' => $this->getTemporaryDirectory() . '/directory-not-exists',
		]);
	}


	public function testShouldRegisterSerializerHandlers(): void
	{
		$container = $this->createContainer([
			'temporaryDirectory' => $this->getTemporaryDirectory(),
			'serializationHandlers' => [
				EmailHandler::class,

			],
		]);

		/** @var \AipNg\JsonSerializer\JsonSerializerInterface $serializer */
		$serializer = $container->getByType(JsonSerializerInterface::class);

		$email = new Email('example@example.org');

		$this->assertSame('"example@example.org"', $serializer->serialize($email));
	}


	public function testShouldThrowExceptionOnInvalidSerializerHandler(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createContainer([
			'temporaryDirectory' => $this->getTemporaryDirectory(),
			'serializationHandlers' => [
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

		$class = $loader->load(
			function (Compiler $compiler) use ($extensionConfig): void {
				$compiler->addConfig([
					self::EXTENSION_NAME => $extensionConfig,
				]);

				$compiler->addExtension(self::EXTENSION_NAME, new JsonSerializerExtension);
			},
			$this->getGeneratedContainerKey(),
		);

		/** @var \Nette\DI\Container */
		return new $class;
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
