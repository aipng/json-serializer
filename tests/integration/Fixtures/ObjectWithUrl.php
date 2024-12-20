<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Integration\Fixtures;

use AipNg\ValueObjects\Web\Url;
use JMS\Serializer\Annotation as Serializer;

final class ObjectWithUrl
{

	#[Serializer\Type(Url::class)]
	private ?Url $url;


	public function __construct(?Url $url = null)
	{
		$this->url = $url;
	}


	public function getUrl(): ?Url
	{
		return $this->url;
	}

}
