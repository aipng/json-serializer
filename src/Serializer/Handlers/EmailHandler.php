<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Serializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\ValueObjects\Web\Email;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class EmailHandler implements SubscribingHandlerInterface
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
				'type' => Email::class,
				'method' => 'serializeToJson',
			],
			[
				'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
				'format' => 'json',
				'type' => Email::class,
				'method' => 'deserializeFromJson',
			],
		];
	}


	/**
	 * @param \JMS\Serializer\JsonSerializationVisitor $visitor
	 * @param \AipNg\ValueObjects\Web\Email $email
	 * @param mixed[] $type
	 * @param \JMS\Serializer\Context $context
	 */
	public function serializeToJson(JsonSerializationVisitor $visitor, Email $email, array $type, Context $context): string
	{
		/** @var string */
		return $visitor->visitString($email->getValue(), $type);
	}


	/**
	 * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
	 * @param string $email
	 * @param mixed[] $type
	 * @param \JMS\Serializer\DeserializationContext $context
	 */
	public function deserializeFromJson(JsonDeserializationVisitor $visitor, string $email, array $type, DeserializationContext $context): ?Email
	{
		try {
			return $email ? Email::from($email) : null;
		} catch (\AipNg\ValueObjects\InvalidArgumentException $e) {
			throw new InvalidArgumentException('Unable to deserialize given e-mail!', 0, $e);
		}
	}

}
