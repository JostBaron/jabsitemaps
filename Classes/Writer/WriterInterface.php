<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Writer;

interface WriterInterface
{
    /**
     * Writes a string to the sitemap index file.
     *
     * @param string $data
     *
     * @return void
     */
    public function writeToIndex(string $data): void;

    /**
     * Writes the given data link to the current sitemap file.
     *
     * @param string $data
     */
    public function writeToSitemap(string $data): void;

    /**
     * Called to notify the writer about starting a new sitemap.
     *
     * @return string The new sitemaps URL.
     */
    public function startingSitemap(): string;

    /**
     * Called to notify the writer about finishing a sitemap.
     */
    public function finishedSitemap(): void;

    /**
     * Called to notify the writer about starting a new sitemap index.
     */
    public function startingSitemapIndex(): void;

    /**
     * Called to notify the writer about finishing a sitemap index.
     */
    public function finishedSitemapIndex(): void;
}
