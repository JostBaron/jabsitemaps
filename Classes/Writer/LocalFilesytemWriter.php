<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Writer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class LocalFilesytemWriter implements WriterInterface
{
    /**
     * @var string
     */
    private $indexFileName;

    /**
     * @var resource
     */
    private $currentIndexFileHandle = null;

    /**
     * @var resource
     */
    private $currentSitemapFileHandle = null;

    /**
     * @var int
     */
    private $currentSitemapNumber = 0;

    /**
     * @param string $indexFileName
     */
    public function __construct(string $indexFileName)
    {
        $this->indexFileName = $indexFileName;
    }

    public function writeToSitemap(string $data): void
    {
        if (null === $this->currentSitemapFileHandle) {
            throw new \RuntimeException(
                'This is a bug - no open sitemap file, but an entry should be written.',
                1592070168
            );
        }

        $result = \fwrite($this->currentSitemapFileHandle, $data);
        if (false === $result) {
            throw new \RuntimeException(
                'Failed writing sitemap entry.',
                1592070697
            );
        }
    }

    public function writeToIndex(string $data): void
    {
        if (null === $this->currentIndexFileHandle) {
            throw new \RuntimeException(
                'This is a bug - no open sitemap index file, but an entry should be written.',
                1592073471
            );
        }

        $result = \fwrite($this->currentIndexFileHandle, $data);
        if (false === $result) {
            throw new \RuntimeException(
                'Failed writing sitemap index entry.',
                1592070762
            );
        }
    }

    public function startingSitemap(): string
    {
        if (null !== $this->currentSitemapFileHandle) {
            throw new \RuntimeException(
                'This is a bug - new sitemap was started while old one was not finished.',
                1592069148
            );
        }

        $filename = $this->getRealFilenameForCurrentSitemap();
        $this->currentSitemapFileHandle = \fopen($filename, 'w');
        if (false === $this->currentSitemapFileHandle) {
            throw new \RuntimeException(
                \sprintf('Could not open sitemap file "%s" for writing.', $filename),
                1592069343
            );
        }

        $currentSitemapFilePath = $this->getRealFilenameForCurrentSitemap();
        $pathComponent = PathUtility::getAbsoluteWebPath($currentSitemapFilePath);
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . $pathComponent;

        return $url;
    }

    public function finishedSitemap(): void
    {
        if (null === $this->currentSitemapFileHandle) {
            throw new \RuntimeException(
                'This is a bug - sitemap was finished but no file handle was open.',
                1592069368
            );
        }

        $result = \fflush($this->currentSitemapFileHandle);
        if (true !== $result) {
            throw new \RuntimeException(
                'Failed to flush sitemap file.',
                1592069455
            );
        }
        $result = \fclose($this->currentSitemapFileHandle);
        if (true !== $result) {
            throw new \RuntimeException(
                'Failed to close sitemap file.',
                1592069512
            );
        }

        $this->currentSitemapFileHandle = null;
        $this->currentSitemapNumber++;
    }

    public function startingSitemapIndex(): void
    {
        if (null !== $this->currentIndexFileHandle) {
            throw new \RuntimeException(
                'This is a bug - new sitemap index was started while old one was not finished.',
                1592069583
            );
        }

        $filename = $this->getRealFilenameForSitemapIndex();
        $this->currentIndexFileHandle = \fopen($filename, 'w');
        if (false === $this->currentIndexFileHandle) {
            throw new \RuntimeException(
                \sprintf('Could not open sitemap index file "%s" for writing.', $filename),
                1592069609
            );
        }
    }

    public function finishedSitemapIndex(): void
    {
        if (null === $this->currentIndexFileHandle) {
            throw new \RuntimeException(
                'This is a bug - sitemap index was finished but no file handle was open.',
                1592069640
            );
        }

        $result = \fflush($this->currentIndexFileHandle);
        if (true !== $result) {
            throw new \RuntimeException(
                'Failed to flush sitemap index file.',
                1592069655
            );
        }
        $result = \fclose($this->currentIndexFileHandle);
        if (true !== $result) {
            throw new \RuntimeException(
                'Failed to close sitemap index file.',
                1592069659
            );
        }

        $this->currentIndexFileHandle = null;
    }

    private function getRealFilenameForSitemapIndex(): string
    {
        return \realpath(PATH_site . $this->indexFileName);
    }

    private function getRealFilenameForCurrentSitemap(): string
    {
        $filenamePrefix = PathUtility::basename($this->getRealFilenameForSitemapIndex());

        $positionOfFirstDot = \strpos($filenamePrefix, '.', 0);
        if (false === $positionOfFirstDot) {
            $baseName = $filenamePrefix;
            $divider = '';
            $extension = '';
        } else {
            $baseName = \substr($filenamePrefix, 0, $positionOfFirstDot);
            $divider = '.';
            $extension = \substr($filenamePrefix, $positionOfFirstDot + 1);
        }

        return \sprintf(
            '%2$s%1$s%3$s-%6$d%4$s%5$s',
            \DIRECTORY_SEPARATOR,
            $this->getRealDirectoryName(),
            $baseName,
            $divider,
            $extension,
            $this->currentSitemapNumber
        );
    }

    private function getRealDirectoryName(): string
    {
        return PathUtility::dirname($this->getRealFilenameForSitemapIndex());
    }
}
