<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Command;

use Jbaron\Jbsitemap\RendererRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSitemapsCommand extends Command
{
    private const ARGUMENT_RENDERER_KEY = 'renderer-key';
    private const OPTION_MAXIMAL_ENTRIES_PER_FILE = 'maximal-entries-per-file';

    protected function configure()
    {
        $this
            ->setName('jbsitemaps:generatesitemaps')
            ->setDescription('Generate all registered sitemaps.')
            ->addArgument(
                self::ARGUMENT_RENDERER_KEY,
                InputArgument::REQUIRED,
                'The sitemap renderer to use'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rendererKey = $input->getArgument(self::ARGUMENT_RENDERER_KEY);

        if (!RendererRegistry::hasRendererWithKey($rendererKey)) {
            throw new \InvalidArgumentException(
                \sprintf('No renderer with key "%s" registered.', $rendererKey),
                1594395972
            );
        }

        $renderer = RendererRegistry::getRenderer($rendererKey);

        $renderingResult = $renderer->render();

        $output->writeln(
            \sprintf(
                'Wrote %d entries into %d sitemaps.',
                $renderingResult->getNumberEntries(),
                $renderingResult->getNumberSitemaps()
            )
        );
    }
}
