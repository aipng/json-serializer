<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer\Adapter;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

final class JmsJsonSerializerAdapter implements
	\AipNg\JsonSerializer\Serializer\JsonSerializerInterface
{

	private const JSON = 'json';

	/** @var \JMS\Serializer\Handler\SubscribingHandlerInterface[] */
	private $handlers = [];

	/** @var string|NULL */
	private $annotationCacheDir;

	/** @var bool */
	private $productionMode = true;

	/** @var \JMS\Serializer\Serializer|null */
	private $serializer;


	/**
	 * @inheritdoc
	 */
	public function serialize($data): string
	{
		return $this->getSerializer()->serialize($data, self::JSON);
	}


	/**
	 * @inheritdoc
	 */
	public function deserialize(string $json, string $type)
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


	private function getSerializer(): Serializer
	{
		if (!$this->serializer) {
			$this->serializer = $this->createSerializer();
		}

		return $this->serializer;
	}


	private function createSerializer(): Serializer
	{
		AnnotationRegistry::registerLoader('class_exists');

		$builder = SerializerBuilder::create();
		$builder
			->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy)
			->setSerializationContextFactory(function (): SerializationContext {
				return SerializationContext::create()->setSerializeNull(true);
			})
			->setDeserializationContextFactory(function (): DeserializationContext {
				return DeserializationContext::create()->setSerializeNull(true);
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
