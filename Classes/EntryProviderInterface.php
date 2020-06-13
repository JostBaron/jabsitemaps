<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap;

interface EntryProviderInterface
{
    /**
     * Returns sitemap entries. May just return an array of entries, but may also
     * use a generator or the "yield" keyword.
     *
     * @return \Iterator
     */
    public function getSitemapEntries(): \Iterator;
}
