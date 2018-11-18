<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Adapter\JmsJsonSerializerAdapter;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

final class JsonSerializerExtension extends CompilerExtension
{

	public const CONFIG_TEMPORARY_DIRECTORY = 'temporaryDirectory';
	public const CONFIG_SERIALIZATION_HANDLERS = 'serializationHandlers';
	public const CONFIG_PRODUCTION_MODE = 'productionMode';

	private const CACHE_DIRECTORY_NAME = 'JmsJsonSerializer.metadata';

	/** @var mixed[] */
	private $defaults = [
		self::CONFIG_TEMPORARY_DIRECTORY => null,
		self::CONFIG_PRODUCTION_MODE => true,
		self::CONFIG_SERIALIZATION_HANDLERS => [],
	];


	public function loadConfiguration(): void
	{
		$this->validateConfig($this->defaults);

		$builder = $this->getContainerBuilder();

		$this->registerSerializer($builder);
	}


	private function registerSerializer(ContainerBuilder $builder): void
	{
		$serializerDefinition = $this->registerSerializerDefinition($builder);

		$this->registerSerializationHandlers($builder, $serializerDefinition);
	}


	private function getCacheDirectory(): string
	{
		$temporaryDirectory = $this->config[self::CONFIG_TEMPORARY_DIRECTORY];

		if (!($temporaryDirectory && is_writable($temporaryDirectory))) {
			throw new InvalidArgumentException(sprintf(
				'Temporary directory must be writable (\'%s\' given)!',
				$temporaryDirectory
			));
		}

		$cacheDirectory = sprintf(
			'%s%s%s',
			$temporaryDirectory,
			DIRECTORY_SEPARATOR,
			self::CACHE_DIRECTORY_NAME
		);

		return $cacheDirectory;
	}


	private function registerSerializerDefinition(ContainerBuilder $builder): ServiceDefinition
	{
		return $builder
			->addDefinition($this->prefix('serializer'))
			->setFactory(JmsJsonSerializerAdapter::class)
			->addSetup('setCache', [
				$this->getCacheDirectory(),
			])
			->addSetup('setProductionMode', [
				$this->config[self::CONFIG_PRODUCTION_MODE],
			]);
	}


	private function registerSerializationHandlers(ContainerBuilder $builder, ServiceDefinition $serializerDefinition): void
	{
		$serializationHandlers = $this->config[self::CONFIG_SERIALIZATION_HANDLERS];

		foreach ($serializationHandlers as $serializationHandlerClass) {
			$handlerDefinitionName = $this->registerSerializationHandlerDefinition($builder, $serializationHandlerClass);

			$serializerDefinition->addSetup('addSubscribingHandler', [
				'@' . $handlerDefinitionName,
			]);
		}
	}


	private function registerSerializationHandlerDefinition(ContainerBuilder $builder, string $handler): string
	{
		if (!class_exists($handler)) {
			throw new InvalidArgumentException(sprintf(
				'Serialization handler (\'%s\') does not exist!',
				$handler
			));
		}

		$definitionName = $this->generateSerializationHandlerDefinitionName($handler);

		$builder
			->addDefinition($definitionName)
			->setFactory($handler);

		return $definitionName;
	}


	private function generateSerializationHandlerDefinitionName(string $handler): string
	{
		try {
			$className = (new \ReflectionClass($handler))->getShortName();
		} catch (\ReflectionException $e) {
			$className = $handler;
		}

		return $this->prefix($className);
	}

}
