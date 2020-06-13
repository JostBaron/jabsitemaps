<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Renderer;

use Jbaron\Jbsitemap\Domain\Model\Entry;
use Jbaron\Jbsitemap\Domain\Model\IndexLink;
use Jbaron\Jbsitemap\EntryProviderInterface;
use Jbaron\Jbsitemap\Writer\WriterInterface;

class Sitemap
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
     * Renders a sitemap with entries provided by the given entry providers
     * to the given writer.
     *
     * @param EntryProviderInterface[] $entryProviders
     * @param WriterInterface $writer
     * @param int $maximalNumberEntriesPerSitemap
     *
     * @throws \Exception
     */
    public function buildSitemap(
        array $entryProviders,
        WriterInterface $writer,
        int $maximalNumberEntriesPerSitemap = 50000
    ): void {
        if ($maximalNumberEntriesPerSitemap <= 0) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Maximal number of entries in sitemap must be positive integer, given: %d',
                    $maximalNumberEntriesPerSitemap
                ),
                1592073683
            );
        }

        $numberEntriesWritten = 0;
        $numberEntriesWrittenForCurrentSitemap = 0;

        $writer->startingSitemapIndex();
        $writer->writeToIndex(\trim(self::SITEMAP_INDEX_START));

        $currentSitemapLink = null;
        foreach ($entryProviders as $entryGenerator) {
            foreach ($entryGenerator->getSitemapEntries() as $sitemapEntry) {
                if ($numberEntriesWrittenForCurrentSitemap >= $maximalNumberEntriesPerSitemap) {
                    $writer->writeToSitemap(\trim(self::SITEMAP_END));
                    $writer->finishedSitemap();

                    $numberEntriesWrittenForCurrentSitemap = 0;
                    $writer->writeToIndex($this->renderSitemapIndexEntry($currentSitemapLink));
                    $currentSitemapLink = null;
                }

                if (null === $currentSitemapLink) {
                    // Start new sitemap.
                    $url = $writer->startingSitemap();
                    $writer->writeToSitemap(\trim(self::SITEMAP_START));

                    $currentSitemapLink = new IndexLink(
                        $url,
                        new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
                    );
                }

                $writer->writeToSitemap($this->renderSitemapEntry($sitemapEntry));
                $numberEntriesWritten++;
                $numberEntriesWrittenForCurrentSitemap++;
            }
        }
        if (null !== $currentSitemapLink) {
            $writer->writeToSitemap(\trim(self::SITEMAP_END));
            $writer->finishedSitemap();

            $writer->writeToIndex($this->renderSitemapIndexEntry($currentSitemapLink));
            $currentSitemapLink = null;
        }

        $writer->writeToIndex(\trim(self::SITEMAP_INDEX_END));
        $writer->finishedSitemapIndex();
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
                \htmlspecialchars($entry->getPriority(), ENT_XML1)
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
