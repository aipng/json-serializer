<?php

declare(strict_types = 1);

namespace AipNg\JsonSerializerTests\Serializer\Handlers\TestObject;

use AipNg\ValueObjects\Web\Url;
use JMS\Serializer\Annotation as Serializer;

final class UrlObject
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
