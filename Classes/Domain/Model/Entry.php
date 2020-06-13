<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

class Entry
{
    public const CHANGE_FREQUENCY_ALWAYS = 'always';
    public const CHANGE_FREQUENCY_HOURLY = 'hourly';
    public const CHANGE_FREQUENCY_DAILY = 'daily';
    public const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    public const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    public const CHANGE_FREQUENCY_YEARLY = 'yearly';
    public const CHANGE_FREQUENCY_NEVER = 'never';

    public const CHANGE_FREQUENCIES = [
        self::CHANGE_FREQUENCY_ALWAYS,
        self::CHANGE_FREQUENCY_HOURLY,
        self::CHANGE_FREQUENCY_DAILY,
        self::CHANGE_FREQUENCY_WEEKLY,
        self::CHANGE_FREQUENCY_MONTHLY,
        self::CHANGE_FREQUENCY_YEARLY,
        self::CHANGE_FREQUENCY_NEVER,
    ];

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastModification;

    /**
     * @var string|null
     */
    private $changeFrequency;

    /**
     * @var float|null
     */
    private $priority;

    /**
     * Entry constructor.
     * @param string $url
     * @param \DateTimeImmutable|null $lastModification
     * @param string|null $changeFrequency
     * @param float|null $priority
     */
    public function __construct(
        string $url,
        ?\DateTimeImmutable $lastModification = null,
        ?string $changeFrequency = null,
        ?float $priority = null
    ) {
        $this->url = $url;
        $this->lastModification = $lastModification;
        $this->changeFrequency = $changeFrequency;
        $this->priority = $priority;
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

    /**
     * @return bool
     */
    public function hasChangeFrequency(): bool
    {
        return null !== $this->changeFrequency;
    }

    /**
     * @return string|null
     */
    public function getChangeFrequency(): ?string
    {
        return $this->changeFrequency;
    }

    /**
     * @return bool
     */
    public function hasPriority(): bool
    {
        return null !== $this->priority;
    }

    /**
     * @return float|null
     */
    public function getPriority(): ?float
    {
        return $this->priority;
    }
}
