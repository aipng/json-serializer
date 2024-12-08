<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\DI;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\JsonSerializer\JmsJsonSerializer;
use AipNg\JsonSerializer\Validator\NullValidator;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @property \stdClass $config
 */
final class JsonSerializerExtension extends CompilerExtension
{

	private const string CACHE_DIRECTORY_NAME = 'AipNg.JsonSerializer';


	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'temporaryDirectory' => Expect::string(),
			'productionMode' => Expect::bool(true),
			'serializationHandlers' => Expect::arrayOf(Expect::string())->default([]),
			'validator' => Expect::anyOf(
				Expect::string(),
				Expect::type(Statement::class),
			)->default(NullValidator::class),
		]);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$serializerDefinition = $builder
			->addDefinition($this->prefix('serializer'))
			->setFactory(JmsJsonSerializer::class)
			->setArgument(
				'validator',
				$this->getValidatorDefinition($builder),
			)
			->addSetup('setCache', [
				$this->getCacheDirectory(),
			])
			->addSetup('setProductionMode', [
				$this->config->productionMode,
			]);

		$this->registerSerializationHandlers($builder, $serializerDefinition);
	}


	private function getValidatorDefinition(ContainerBuilder $builder): ServiceDefinition
	{
		return $builder
			->addDefinition($this->prefix('validator'))
			->setFactory(
				$this->config->validator instanceof Statement
					? $this->config->validator
					: new Statement($this->config->validator),
			);
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
