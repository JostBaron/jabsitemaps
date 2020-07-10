<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Exception;

/**
 * Exception that is thrown if a renderer is requested for a key for which no renderer is registered.
 *
 * @package Jbaron\Jbsitemap\Exception
 */
class RendererWithKeyMissingException extends \RuntimeException
{
}
