<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

use Jbaron\Jbsitemap\EntryProviderInterface;
use Jbaron\Jbsitemap\Writer\WriterInterface;

class Renderer
{
    private const SITEMAP_INDEX_START = <<<'SITEMAP_INDEX_START'
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
SITEMAP_INDEX_START;

    private const SITEMAP_INDEX_END = <<<'SITEMAP_INDEX_END'
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
SITEMAP_INDEX_END;

    private const SITEMAP_START = <<<'SITEMAP_START'
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
SITEMAP_START;

    private const SITEMAP_END = <<<'SITEMAP_END'
</urlset>
SITEMAP_END;

    /**
     * @var string
     */
    private $key;

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
     * @param string $key
     * @param EntryProviderInterface[] $entryProviders
     * @param WriterInterface $writer
     * @param int $maximalNumberEntriesPerSitemap
     */
    public function __construct(
        string $key,
        array $entryProviders,
        WriterInterface $writer,
        int $maximalNumberEntriesPerSitemap
    ) {
        $this->key = $key;
        $this->entryProviders = $entryProviders;
        $this->writer = $writer;
        $this->maximalNumberEntriesPerSitemap = $maximalNumberEntriesPerSitemap;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function render(): RenderingResult
    {
        if ($this->maximalNumberEntriesPerSitemap <= 0) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Maximal number of entries in sitemap must be positive integer, given: %d',
                    $this->maximalNumberEntriesPerSitemap
                ),
                1592073683
            );
        }

        $numberEntriesWritten = 0;
        $numberEntriesWrittenForCurrentSitemap = 0;
        $numberSitemaps = 0;

        $this->writer->startingSitemapIndex();
        $this->writer->writeToIndex(\trim(self::SITEMAP_INDEX_START));

        $currentSitemapLink = null;
        foreach ($this->entryProviders as $entryGenerator) {
            foreach ($entryGenerator->getSitemapEntries() as $sitemapEntry) {
                if ($numberEntriesWrittenForCurrentSitemap >= $this->maximalNumberEntriesPerSitemap) {
                    $this->writer->writeToSitemap(\trim(self::SITEMAP_END));
                    $this->writer->finishedSitemap();

                    $numberEntriesWrittenForCurrentSitemap = 0;
                    $this->writer->writeToIndex($this->renderSitemapIndexEntry($currentSitemapLink));
                    $currentSitemapLink = null;
                }

                if (null === $currentSitemapLink) {
                    // Start new sitemap.
                    $url = $this->writer->startingSitemap();
                    $numberSitemaps++;
                    $this->writer->writeToSitemap(\trim(self::SITEMAP_START));

                    $currentSitemapLink = new IndexLink(
                        $url,
                        new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
                    );
                }

                $this->writer->writeToSitemap($this->renderSitemapEntry($sitemapEntry));
                $numberEntriesWritten++;
                $numberEntriesWrittenForCurrentSitemap++;
            }
        }
        if (null !== $currentSitemapLink) {
            $this->writer->writeToSitemap(\trim(self::SITEMAP_END));
            $this->writer->finishedSitemap();

            $this->writer->writeToIndex($this->renderSitemapIndexEntry($currentSitemapLink));
            $currentSitemapLink = null;
        }

        $this->writer->writeToIndex(\trim(self::SITEMAP_INDEX_END));
        $this->writer->finishedSitemapIndex();

        return new RenderingResult($numberSitemaps, $numberEntriesWritten);
    }

    private function renderSitemapEntry(Entry $entry): string
    {
        $optionalTags = '';
        if ($entry->hasLastModification()) {
            $optionalTags .= \sprintf(
                '<lastmod>%s</lastmod>',
                \htmlspecialchars($entry->getLastModification()->format('c'), ENT_XML1)
            );
        }

        if ($entry->hasChangeFrequency()) {
            $optionalTags .= \sprintf(
                '<changefreq>%s</changefreq>',
                \htmlspecialchars($entry->getChangeFrequency(), ENT_XML1)
            );
        }

        if ($entry->hasPriority()) {
            $optionalTags .= \sprintf(
                '<priority>%d</priority>',
                \htmlspecialchars((string)$entry->getPriority(), ENT_XML1)
            );
        }

        return \sprintf(
            '<url><loc>%s</loc>%s</url>',
            \htmlspecialchars($entry->getUrl(), ENT_XML1),
            $optionalTags
        );
    }

    private function renderSitemapIndexEntry(IndexLink $indexLink): string
    {
        $optionalTags = '';
        if ($indexLink->hasLastModification()) {
            $optionalTags .= \sprintf(
                '<lastmod>%s</lastmod>',
                \htmlspecialchars($indexLink->getLastModification()->format('c'), ENT_XML1)
            );
        }

        return \sprintf(
            '<sitemap><loc>%s</loc>%s</sitemap>',
            \htmlspecialchars($indexLink->getUrl(), ENT_XML1),
            $optionalTags
        );
    }
}
