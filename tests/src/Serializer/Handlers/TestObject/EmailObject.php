<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers\TestObject;

use AipNg\ValueObjects\Web\Email;
use JMS\Serializer\Annotation as Serializer;

final class EmailObject
{

	/**
	 * @var \AipNg\ValueObjects\Web\Email|null
	 *
	 * @Serializer\Type("AipNg\ValueObjects\Web\Email")
	 */
	private $email;


	public function __construct(?Email $email = null)
	{
		$this->email = $email;
	}


	public function getEmail(): ?Email
	{
		return $this->email;
	}

}
