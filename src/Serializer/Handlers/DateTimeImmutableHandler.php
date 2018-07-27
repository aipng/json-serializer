<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class DateTimeImmutableHandler implements
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
				'type' => \DateTimeImmutable::class,
				'method' => 'serializeToJson',
			],
			[
				'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
				'format' => 'json',
				'type' => \DateTimeImmutable::class,
				'method' => 'deserializeFromJson',
			],
		];
	}


	/**
	 * @param \JMS\Serializer\JsonSerializationVisitor $visitor
	 * @param \DateTimeImmutable $immutable
	 * @param mixed[] $type
	 * @param \JMS\Serializer\Context $context
	 */
	public function serializeToJson(
		JsonSerializationVisitor $visitor,
		\DateTimeImmutable $immutable,
		array $type,
		Context $context
	): string
	{
		return $visitor->visitString($immutable->format(DATE_ATOM), $type, $context);
	}


	/**
	 * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
	 * @param string $immutable
	 * @param mixed[] $type
	 * @param \JMS\Serializer\DeserializationContext $context
	 */
	public function deserializeFromJson(
		JsonDeserializationVisitor $visitor,
		string $immutable,
		array $type,
		DeserializationContext $context
	): \DateTimeImmutable
	{
		$dateTimeImmutable = \DateTimeImmutable::createFromFormat(DATE_ATOM, $immutable);

		if (!$dateTimeImmutable) {
			throw new InvalidArgumentException(sprintf(
				'String in DATE_ATOM format expected, \'%s\' given!',
				$immutable
			));
		}

		return $dateTimeImmutable;
	}

}
