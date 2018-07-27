<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\ValueObjects\Web\Url;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class UrlHandler implements
	\JMS\Serializer\Handler\SubscribingHandlerInterface
{

	/**
	 * @return mixed[]
	 */
	public static function getSubscribingMethods(): array
	{
		return [
			[
				'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
				'format' => 'json',
				'type' => Url::class,
				'method' => 'serializeToJson',
			],
			[
				'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
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
		return $visitor->visitString($url->getValue(), $type, $context);
	}


	/**
	 * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
	 * @param string $url
	 * @param mixed[] $type
	 * @param \JMS\Serializer\DeserializationContext $context
	 *
	 * @return \AipNg\ValueObjects\Web\Url
	 */
	public function deserializeFromJson(JsonDeserializationVisitor $visitor, string $url, array $type, DeserializationContext $context): Url
	{
		try {
			$deserializedUrl = new Url($url);
		} catch (\AipNg\ValueObjects\InvalidArgumentException $e) {
			throw new InvalidArgumentException('Unable to deserialize given URL!', 0, $e);
		}

		return $deserializedUrl;
	}

}
