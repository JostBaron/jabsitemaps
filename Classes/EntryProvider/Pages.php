<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\EntryProvider;

use Jbaron\Jbsitemap\Domain\Model\Entry;
use Jbaron\Jbsitemap\EntryProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

class Pages implements EntryProviderInterface
{
    /**
     * ContentObjectRenderer
     *
     * @var	\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var PageRepository
     */
    protected $pagesRepository;

    public function getSitemapEntries(): \Iterator
    {
        /** @var array $rootPage */
        $rootPage = $this->pagesRepository->getDomainStartPage(
            GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST')
        );

        $currentPageList = [$rootPage];

        while ([] !== $currentPageList) {
            $page = \array_unshift($currentPageList);

            yield $this->convertPageArrayToEntry($page);

            $subpages = $this->pagesRepository->getMenu($page['uid'], '*', '', '', false);

            $currentPageList = \array_merge($currentPageList, $subpages);
        }
    }

    private function convertPageArrayToEntry(array $pageData): Entry
    {
        $pageUid = (int)$pageData['uid'];
        $pageUrl = $this->getPageLink($pageUid);

        $lastChanged = (int)$pageData['SYS_LASTCHANGED'];

        $modificationTimestamps = GeneralUtility::intExplode(',', $pageData['tx_ddgooglesitemap_lastmod'], true);

        $lastModification = new \DateTimeImmutable('@' . \max($modificationTimestamps), new \DateTimeZone('UTC'));

        $changeFrequency = $pageData['tx_ddgooglesitemap_change_frequency'];
        if ('' === $changeFrequency) {
            $changeFrequency = $this->calculateChangeFrequency($modificationTimestamps, $lastChanged);
        }

        $priority = ((int)$pageData['tx_ddgooglesitemap_priority'])/10;

        return new Entry($pageUrl, $lastModification, $changeFrequency, $priority);
    }

    /**
     * @param int[] $modificationTimestamps
     *
     * @return string
     */
    protected function calculateChangeFrequency(array $modificationTimestamps, int $lastChanged): string
    {
        $modificationTimestamps[] = $lastChanged;
        $modificationTimestamps[] = \time();

        \sort($modificationTimestamps, SORT_NUMERIC);
        $sum = 0;
        for ($i = \count($modificationTimestamps) - 1; $i > 0; $i--) {
            $sum += $modificationTimestamps[$i] - $modificationTimestamps[$i - 1];
        }

        $average = ($sum/(count($modificationTimestamps) - 1));
        if ($average >= 180*24*60*60) {
            return Entry::CHANGE_FREQUENCY_YEARLY;
        }
        if ($average <= 24*60*60) {
            return Entry::CHANGE_FREQUENCY_DAILY;
        }
        if ($average <= 60*60) {
            return Entry::CHANGE_FREQUENCY_HOURLY;
        }
        if ($average <= 14*24*60*60) {
            return Entry::CHANGE_FREQUENCY_WEEKLY;
        }
        return Entry::CHANGE_FREQUENCY_MONTHLY;
    }

    private function getPageLink(int $pageId): string
    {
        $typolinkConfiguration = array(
            'parameter' => $pageId,
            'returnLast' => 'url',
        );

        $link = htmlspecialchars($this->contentObjectRenderer->typoLink('', $typolinkConfiguration));
        return GeneralUtility::locationHeaderUrl($link);
    }
}
