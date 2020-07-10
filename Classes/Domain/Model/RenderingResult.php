<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

class RenderingResult
{
    /**
     * @var int
     */
    private $numberSitemaps;

    /**
     * @var int
     */
    private $numberEntries;

    /**
     * @param int $numberSitemaps
     * @param int $numberEntries
     */
    public function __construct(int $numberSitemaps, int $numberEntries)
    {
        $this->numberSitemaps = $numberSitemaps;
        $this->numberEntries = $numberEntries;
    }

    /**
     * @return int
     */
    public function getNumberSitemaps(): int
    {
        return $this->numberSitemaps;
    }

    /**
     * @return int
     */
    public function getNumberEntries(): int
    {
        return $this->numberEntries;
    }
}
