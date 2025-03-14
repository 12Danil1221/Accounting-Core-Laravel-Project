<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;

/**
 * @template T of ResourceContract|null
 * @template PT of ResourceContract|null
 */
trait HasResource
{
    /**
     * @var T
     */
    protected ?ResourceContract $resource = null;

    /**
     * @var PT
     */
    protected ?ResourceContract $parentResource = null;

    /**
     * @return PT
     */
    public function getParentResource(): ?ResourceContract
    {
        return $this->parentResource;
    }

    /**
     * @param PT $resource
     */
    public function setParentResource(?ResourceContract $resource): static
    {
        $this->parentResource = $resource;

        return $this;
    }

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function hasResource(): bool
    {
        return ! \is_null($this->resource);
    }

    /**
     * @return ?T
     */
    public function getResource(): ?ResourceContract
    {
        return $this->resource;
    }

    protected function validateResource(): void
    {
        if (! $this->hasResource()) {
            throw ResourceException::required();
        }
    }
}
