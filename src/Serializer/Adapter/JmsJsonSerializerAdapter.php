<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer\Adapter;

use AipNg\JsonSerializer\Serializer\JsonSerializerInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

final class JmsJsonSerializerAdapter implements JsonSerializerInterface
{

	private const JSON = 'json';

	/** @var \JMS\Serializer\Handler\SubscribingHandlerInterface[] */
	private array $handlers = [];

	private ?string $annotationCacheDir = null;

	private bool $productionMode = true;

	private ?SerializerInterface $serializer = null;


	public function serialize(mixed $data): string
	{
		return $this->getSerializer()->serialize($data, self::JSON);
	}


	public function deserialize(string $json, string $type): mixed
	{
		return $this->getSerializer()->deserialize($json, $type, self::JSON);
	}


	public function addSubscribingHandler(SubscribingHandlerInterface $handler): JmsJsonSerializerAdapter
	{
		$this->handlers[] = $handler;

		return $this;
	}


	public function setCache(string $annotationCacheDir): JmsJsonSerializerAdapter
	{
		$this->annotationCacheDir = $annotationCacheDir;

		return $this;
	}


	public function setProductionMode(bool $productionMode): JmsJsonSerializerAdapter
	{
		$this->productionMode = $productionMode;

		return $this;
	}


	private function getSerializer(): SerializerInterface
	{
		if (!$this->serializer) {
			$this->serializer = $this->createSerializer();
		}

		return $this->serializer;
	}


	private function createSerializer(): SerializerInterface
	{
		AnnotationRegistry::registerLoader('class_exists');

		$builder = SerializerBuilder::create();
		$builder
			->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy)
			->setSerializationContextFactory(function (): SerializationContext {
				return SerializationContext::create()->setSerializeNull(true);
			})
			->addDefaultHandlers();

		$this->registerHandlers($builder);

		if ($this->annotationCacheDir) {
			$builder
				->setCacheDir($this->annotationCacheDir)
				->setDebug(!$this->productionMode);
		}

		return $builder->build();
	}


	private function registerHandlers(SerializerBuilder $builder): void
	{
		if ($this->handlers) {
			$builder->configureHandlers(function (HandlerRegistry $registry): void {
				foreach ($this->handlers as $handler) {
					$registry->registerSubscribingHandler($handler);
				}
			});
		}
	}

}
