<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Command;

use Jbaron\Jbsitemap\Domain\Model\EntryProviderDefinition;
use Jbaron\Jbsitemap\Domain\Model\RendererDefinition;
use Jbaron\Jbsitemap\EntryProviderInterface;
use Jbaron\Jbsitemap\Renderer\Sitemap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class GenerateSitemapsCommand extends Command
{
    private const ARGUMENT_RENDERER_DEFINITION = 'renderer-definition';
    private const OPTION_MAXIMAL_ENTRIES_PER_FILE = 'maximal-entries-per-file';

    /**
     * @var Sitemap
     * @inject
     */
    protected $sitemapRenderer;

    protected function configure()
    {
        $this
            ->setName('jbsitemaps:generatesitemaps')
            ->setDescription('Generate all registered sitemaps.')
            ->addArgument(
                self::ARGUMENT_RENDERER_DEFINITION,
                InputArgument::REQUIRED,
                'The sitemap renderer to use'
            )
            ->addOption(
                self::OPTION_MAXIMAL_ENTRIES_PER_FILE,
                'm',
                InputOption::VALUE_REQUIRED,
                'Maximal number of URLs per file',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rendererDefinitionName = $input->getArgument(self::ARGUMENT_RENDERER_DEFINITION);
        $maximalEntriesPerFile = (int)$input->getOption(self::OPTION_MAXIMAL_ENTRIES_PER_FILE);

        $rendererDefinition = $this->getRendererDefinition($rendererDefinitionName);

        if (null === $maximalEntriesPerFile) {
            $maximalEntriesPerFile = $rendererDefinition->getMaximalNumberEntriesPerSitemap();
        }

        /** @var ObjectManagerInterface $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->sitemapRenderer = $objectManager->get(Sitemap::class);

        $entryProviders = \array_map(
            function (EntryProviderDefinition $entryProviderDefinition) use ($objectManager): EntryProviderInterface
            {
                $entryProvider = $objectManager->get($entryProviderDefinition->getInjectionName());

                foreach ($entryProviderDefinition->getArguments() as $key => $value) {
                    $setterName = 'set' . \ucfirst($key);
                    if (\is_callable([$entryProvider, $setterName])) {
                        $entryProvider->$setterName($value);
                    }
                }

                return $entryProvider;
            },
            $rendererDefinition->getEntryProviderDefinitions()
        );

        $this->sitemapRenderer->buildSitemap(
            $entryProviders,
            $rendererDefinition->getWriter(),
            $maximalEntriesPerFile
        );
    }

    private function getRendererDefinition(string $name): RendererDefinition
    {
        if (!\is_array($GLOBALS['TYPO3_CONF_VARS']['tx_jbsitemaps']['renderers'])) {
            throw new \InvalidArgumentException(
                'No sitemap renderer definitions.',
                1592084164
            );
        }

        foreach ($GLOBALS['TYPO3_CONF_VARS']['tx_jbsitemaps']['renderers'] as $index => $rendererDefinition) {
            if (!($rendererDefinition instanceof RendererDefinition)) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Sitemap renderer definition with index "%d" is not an instance of "%s".',
                        $index,
                        RendererDefinition::class
                    ),
                    1592084164
                );
            }

            if ($name === $rendererDefinition->getName()) {
                return $rendererDefinition;
            }
        }

        throw new \InvalidArgumentException(
            \sprintf('No renderer definition for name "%s" found.', $name),
            1592085528
        );
    }
}
