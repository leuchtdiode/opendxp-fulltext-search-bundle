<?php
declare(strict_types=1);

namespace Try2catch\OpenDxp\FulltextSearchBundle\Model;

class SearchResultItem
{
	public function __construct(
		private readonly string $id,
		private readonly string $url,
		private readonly string $title,
		private readonly string $description,
		private readonly ?string $payload
	)
	{
	}

	public function getPayloadDecoded(): ?array
	{
		return $this->payload && json_validate($this->payload)
			? json_decode($this->payload, true)
			: null;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getPayload(): ?string
	{
		return $this->payload;
	}
}
