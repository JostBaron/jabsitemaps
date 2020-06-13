<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

use Jbaron\Jbsitemap\EntryProviderInterface;
use Jbaron\Jbsitemap\Writer\WriterInterface;

class RendererDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntryProviderInterface[]
     */
    private $entryProviders;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var int
     */
    private $maximalNumberEntriesPerSitemap = 50000;

    /**
     * RendererDefinition constructor.
     * @param string $name
     * @param EntryProviderInterface[] $entryProviders
     * @param WriterInterface $writer
     * @param int $maximalNumberEntriesPerSitemap
     */
    public function __construct(
        string $name,
        array $entryProviders,
        WriterInterface $writer,
        int $maximalNumberEntriesPerSitemap
    ) {
        $this->name = $name;
        $this->entryProviders = $entryProviders;
        $this->writer = $writer;
        $this->maximalNumberEntriesPerSitemap = $maximalNumberEntriesPerSitemap;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return EntryProviderInterface[]
     */
    public function getEntryProviders(): array
    {
        return $this->entryProviders;
    }

    /**
     * @return WriterInterface
     */
    public function getWriter(): WriterInterface
    {
        return $this->writer;
    }

    /**
     * @return int
     */
    public function getMaximalNumberEntriesPerSitemap(): int
    {
        return $this->maximalNumberEntriesPerSitemap;
    }
}
