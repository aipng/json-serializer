<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration\Fixtures;

use AipNg\ValueObjects\Web\Email;
use AipNg\ValueObjects\Web\Url;

final readonly class ObjectWithEmailAndUrl
{

	public function __construct(
		public Email $email,
		public Url $url,
	)
	{
	}

}
