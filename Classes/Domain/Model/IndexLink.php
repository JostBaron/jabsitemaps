<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

class IndexLink
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastModification;

    /**
     * @param string $url
     * @param \DateTimeImmutable|null $lastModification
     */
    public function __construct(string $url, ?\DateTimeImmutable $lastModification = null)
    {
        $this->url = $url;
        $this->lastModification = $lastModification;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function hasLastModification(): bool
    {
        return null !== $this->lastModification;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastModification(): ?\DateTimeImmutable
    {
        return $this->lastModification;
    }
}
