<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\ValueObjects\InvalidArgumentException as UrlArgumentException;
use AipNg\ValueObjects\Web\Url;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class UrlHandler implements SubscribingHandlerInterface
{

	/**
	 * @return mixed[]
	 */
	public static function getSubscribingMethods(): array
	{
		return [
			[
				'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
				'format' => 'json',
				'type' => Url::class,
				'method' => 'serializeToJson',
			],
			[
				'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
				'format' => 'json',
				'type' => Url::class,
				'method' => 'deserializeFromJson',
			],
		];
	}


	/**
	 * @param \JMS\Serializer\JsonSerializationVisitor $visitor
	 * @param \AipNg\ValueObjects\Web\Url $url
	 * @param mixed[] $type
	 * @param \JMS\Serializer\Context $context
	 *
	 * @return string
	 */
	public function serializeToJson(JsonSerializationVisitor $visitor, Url $url, array $type, Context $context): string
	{
		/** @var string */
		return $visitor->visitString($url->getValue(), $type);
	}


	/**
	 * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
	 * @param mixed $url
	 * @param mixed[] $type
	 * @param \JMS\Serializer\DeserializationContext $context
	 *
	 * @return \AipNg\ValueObjects\Web\Url
	 */
	public function deserializeFromJson(JsonDeserializationVisitor $visitor, mixed $url, array $type, DeserializationContext $context): Url
	{
		try {
			if (!is_string($url)) {
				throw new InvalidArgumentException('Url must be a string!', 404);
			}

			return Url::from($url);
		} catch (UrlArgumentException $e) {
			$exception = new InvalidArgumentException('Invalid input!', 404, $e);

			throw $exception
				->withError(
					join('.', $context->getCurrentPath()),
					$e->getMessage(),
				);
		}
	}

}
