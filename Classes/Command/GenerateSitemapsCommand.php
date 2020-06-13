<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Command;

use Jbaron\Jbsitemap\Domain\Model\RendererDefinition;
use Jbaron\Jbsitemap\Renderer\Sitemap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $this->sitemapRenderer->buildSitemap(
            $rendererDefinition->getEntryProviders(),
            $rendererDefinition->getWriter(),
            $maximalEntriesPerFile
        );
    }

    private function getRendererDefinition(string $name): RendererDefinition
    {
        if (!\array_key_exists($name, $GLOBALS['tx_jbsitemaps']['renderers'])) {
            throw new \InvalidArgumentException(
                \sprintf('No sitemap renderer definition with name "%s" found.', $name),
                1592084164
            );
        }

        $rendererDefinition = $GLOBALS['tx_jbsitemaps']['renderers'][$name];
        if (!($rendererDefinition instanceof RendererDefinition)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Sitemap renderer definition with name "%s" is not an instance of "%s".',
                    $name,
                    RendererDefinition::class
                ),
                1592084164
            );
        }

        return $rendererDefinition;
    }
}
