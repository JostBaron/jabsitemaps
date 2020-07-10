<?php

declare(strict_types=1);

namespace Jbaron\Jbsitemap\Domain\Model;

class EntryProviderDefinition
{
    /**
     * @var string
     */
    private $injectionName;

    /**
     * @var mixed[]
     */
    private $arguments;

    /**
     * EntryProviderDefinition constructor.
     * @param string $injectionName
     * @param mixed[] $arguments
     */
    public function __construct(string $injectionName, array $arguments = [])
    {
        $this->injectionName = $injectionName;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getInjectionName(): string
    {
        return $this->injectionName;
    }

    /**
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
