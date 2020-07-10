<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

use Jbaron\Jbsitemap\Writer\WriterInterface;

class RendererDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntryProviderDefinition[]
     */
    private $entryProviderDefinitions;

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
     * @param EntryProviderDefinition[] $entryProviderDefinitions
     * @param WriterInterface $writer
     * @param int $maximalNumberEntriesPerSitemap
     */
    public function __construct(
        string $name,
        array $entryProviderDefinitions,
        WriterInterface $writer,
        int $maximalNumberEntriesPerSitemap
    ) {
        $this->name = $name;
        $this->entryProviderDefinitions = $entryProviderDefinitions;
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
     * @return EntryProviderDefinition[]
     */
    public function getEntryProviderDefinitions(): array
    {
        return $this->entryProviderDefinitions;
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
