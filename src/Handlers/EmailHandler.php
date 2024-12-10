<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializer\Handlers;

use AipNg\JsonSerializer\InvalidArgumentException;
use AipNg\ValueObjects\InvalidArgumentException as EmailArgumentException;
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
	 *
	 * @return string
	 */
	public function serializeToJson(JsonSerializationVisitor $visitor, Email $email, array $type, Context $context): string
	{
		/** @var string */
		return $visitor->visitString($email->getValue(), $type);
	}


	/**
	 * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
	 * @param mixed $email
	 * @param mixed[] $type
	 * @param \JMS\Serializer\DeserializationContext $context
	 *
	 * @return \AipNg\ValueObjects\Web\Email
	 */
	public function deserializeFromJson(JsonDeserializationVisitor $visitor, mixed $email, array $type, DeserializationContext $context): Email
	{
		try {
			if (!is_string($email)) {
				throw new InvalidArgumentException('Email must be a string!', 404);
			}

			return Email::from($email);
		} catch (EmailArgumentException $e) {
			$exception = new InvalidArgumentException('Invalid input!', 404, $e);

			throw $exception
				->withError(
					join('.', $context->getCurrentPath()),
					$e->getMessage(),
				);
		}
	}

}
