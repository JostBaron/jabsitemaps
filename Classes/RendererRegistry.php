<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap;

use Jbaron\Jbsitemap\Domain\Model\Renderer;
use Jbaron\Jbsitemap\Exception\DuplicatedRendererKeyException;
use Jbaron\Jbsitemap\Exception\RendererWithKeyMissingException;

class RendererRegistry
{
    /**
     * Associative array mapping sitemap renderer names to the renderers.
     *
     * @var Renderer[]
     */
    protected static $registeredRenderers = [];

    /**
     * Registers the given renderer with its key as key.
     *
     * @param Renderer $renderer
     */
    public static function registerRenderer(Renderer $renderer): void
    {
        if (\array_key_exists($renderer->getKey(), static::$registeredRenderers)) {
            throw new DuplicatedRendererKeyException(
                \sprintf('A sitemap renderer with key "%s" was already registered.', $renderer->getKey()),
                1594394863
            );
        }

        static::$registeredRenderers[$renderer->getKey()] = $renderer;
    }

    /**
     * Replaces the renderer with the given key by the given renderer - independent if the
     * given renderer has the same key. If no renderer is registered with the given key,
     * the given renderer is registered with the given key.
     *
     * @param string $key
     * @param Renderer $replacementRenderer
     */
    public static function replaceRenderer(string $key, Renderer $replacementRenderer): void
    {
        static::$registeredRenderers[$key] = $replacementRenderer;
    }

    public static function hasRendererWithKey(string $key): bool
    {
        return \array_key_exists($key, static::$registeredRenderers);
    }

    public static function getRenderer(string $key): Renderer
    {
        if (!static::hasRendererWithKey($key)) {
            throw new RendererWithKeyMissingException(
                \sprintf('No renderer with key "%s" was registered.', $key),
                1594395173
            );
        }

        return static::$registeredRenderers[$key];
    }
}
