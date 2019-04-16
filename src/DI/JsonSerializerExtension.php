<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\Serializer\Adapter\JmsJsonSerializerAdapter;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class JsonSerializerExtension extends CompilerExtension
{

	private const CACHE_DIRECTORY_NAME = 'JmsJsonSerializer.metadata';

	/** @var \AipNg\JsonSerializer\DI\JsonSerializerConfig */
	protected $config;


	public function getConfigSchema(): Schema
	{
		return Expect::from(new JsonSerializerConfig);
	}


	public function loadConfiguration(): void
	{
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
		$temporaryDirectory = $this->config->temporaryDirectory;

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
				$this->config->productionMode,
			]);
	}


	private function registerSerializationHandlers(ContainerBuilder $builder, ServiceDefinition $serializerDefinition): void
	{
		foreach ($this->config->serializationHandlers as $serializationHandlerClass) {
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
