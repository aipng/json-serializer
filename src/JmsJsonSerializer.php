<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

final class JmsJsonSerializer implements JsonSerializer
{

	private const string JSON = 'json';

	/** @var \JMS\Serializer\Handler\SubscribingHandlerInterface[] */
	private array $handlers = [];

	private ?string $cacheDir = null;

	private bool $productionMode = true;

	private ?SerializerInterface $serializer = null;


	public function __construct(private readonly Validator $validator)
	{
	}


	public function serialize(mixed $data): string
	{
		return $this->getSerializer()->serialize($data, self::JSON);
	}


	public function deserialize(string $json, string $type): mixed
	{
		try {
			$object = $this->getSerializer()->deserialize($json, $type, self::JSON);
		} catch (InvalidArgumentException $e) {
			$exception = new InvalidArgumentException('Invalid JSON input!', $e->getCode(), $e);

			foreach ($e->getContext() as $key => $messages) {
				foreach ($messages as $message) {
					$exception->withError($key, $message);
				}
			}

			throw $exception;
		}

		$this->validator->validate($object);

		return $object;
	}


	public function addSubscribingHandler(SubscribingHandlerInterface $handler): self
	{
		$this->handlers[] = $handler;

		return $this;
	}


	public function setCache(string $cacheDir): self
	{
		$this->cacheDir = $cacheDir;

		return $this;
	}


	public function setProductionMode(bool $productionMode): self
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
		$builder = SerializerBuilder::create();
		$builder
			->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy)
			->setSerializationContextFactory(function (): SerializationContext {
				return SerializationContext::create()->setSerializeNull(true);
			})
			->enableEnumSupport()
			->addDefaultHandlers();

		$this->registerHandlers($builder);

		if ($this->cacheDir) {
			$builder
				->setCacheDir($this->cacheDir)
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
