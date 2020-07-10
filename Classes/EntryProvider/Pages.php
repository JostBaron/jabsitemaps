<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\EntryProvider;

use Jbaron\Jbsitemap\Domain\Model\Entry;
use Jbaron\Jbsitemap\EntryProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Pages implements EntryProviderInterface
{
    /**
     * @var	\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     * @inject
     */
    protected $contentObjectRenderer;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pagesRepository;

    /**
     * @var string
     */
    private $domain;

    public function getSitemapEntries(): \Iterator
    {
        $rootPageId = $this->pagesRepository->getDomainStartPage($this->domain);
        /** @var array $rootPage */
        $rootPage = $this->pagesRepository->getPage((int)$rootPageId);

        $currentPageList = [$rootPage];
        while ([] !== $currentPageList) {
            $page = \array_shift($currentPageList);

            yield $this->convertPageArrayToEntry($page);

            $subpages = $this->pagesRepository->getMenu($page['uid'], '*', '', '', false);

            $currentPageList = \array_merge($currentPageList, $subpages);
        }
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    private function convertPageArrayToEntry(array $pageData): Entry
    {
        $pageUid = (int)$pageData['uid'];
        $pageUrl = $this->getPageLink($pageUid);

        $lastChanged = (int)$pageData['SYS_LASTCHANGED'];

        $modificationTimestamps = GeneralUtility::intExplode(',', $pageData['tx_ddgooglesitemap_lastmod'], true);

        $lastModification = new \DateTimeImmutable(
            '@' . \max([] === $modificationTimestamps ? [0] : $modificationTimestamps),
            new \DateTimeZone('UTC')
        );

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
    private function calculateChangeFrequency(array $modificationTimestamps, int $lastChanged): string
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
        $typolinkConfiguration = [
            'parameter' => $pageId,
            'returnLast' => 'url',
        ];

        $link = htmlspecialchars($this->contentObjectRenderer->typoLink('', $typolinkConfiguration));
        return GeneralUtility::locationHeaderUrl($link);
    }
}
