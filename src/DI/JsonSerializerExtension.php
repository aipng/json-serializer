<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

use AipNg\JsonSerializer\Adapter\JmsJsonSerializerAdapter;
use AipNg\JsonSerializer\InvalidArgumentException;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @property \stdClass $config
 */
final class JsonSerializerExtension extends CompilerExtension
{

	private const string CACHE_DIRECTORY_NAME = 'JmsJsonSerializer.metadata';


	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'temporaryDirectory' => Expect::string(),
			'productionMode' => Expect::bool(false),
			'serializationHandlers' => Expect::arrayOf(Expect::string())->default([]),
		]);
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
				$temporaryDirectory,
			));
		}

		return sprintf(
			'%s%s%s',
			$temporaryDirectory,
			DIRECTORY_SEPARATOR,
			self::CACHE_DIRECTORY_NAME,
		);
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
				$handler,
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
		} catch (\ReflectionException) {
			$className = $handler;
		}

		return $this->prefix($className);
	}

}
