<?php

declare(strict_types=1);

namespace EDD\Vendor\Square\Models\Builders;

use EDD\Vendor\Core\Utils\CoreHelper;
use EDD\Vendor\Square\Models\ListOrderCustomAttributeDefinitionsRequest;

/**
 * Builder for model ListOrderCustomAttributeDefinitionsRequest
 *
 * @see ListOrderCustomAttributeDefinitionsRequest
 */
class ListOrderCustomAttributeDefinitionsRequestBuilder
{
    /**
     * @var ListOrderCustomAttributeDefinitionsRequest
     */
    private $instance;

    private function __construct(ListOrderCustomAttributeDefinitionsRequest $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Initializes a new List Order Custom Attribute Definitions Request Builder object.
     */
    public static function init(): self
    {
        return new self(new ListOrderCustomAttributeDefinitionsRequest());
    }

    /**
     * Sets visibility filter field.
     *
     * @param string|null $value
     */
    public function visibilityFilter(?string $value): self
    {
        $this->instance->setVisibilityFilter($value);
        return $this;
    }

    /**
     * Sets cursor field.
     *
     * @param string|null $value
     */
    public function cursor(?string $value): self
    {
        $this->instance->setCursor($value);
        return $this;
    }

    /**
     * Unsets cursor field.
     */
    public function unsetCursor(): self
    {
        $this->instance->unsetCursor();
        return $this;
    }

    /**
     * Sets limit field.
     *
     * @param int|null $value
     */
    public function limit(?int $value): self
    {
        $this->instance->setLimit($value);
        return $this;
    }

    /**
     * Unsets limit field.
     */
    public function unsetLimit(): self
    {
        $this->instance->unsetLimit();
        return $this;
    }

    /**
     * Initializes a new List Order Custom Attribute Definitions Request object.
     */
    public function build(): ListOrderCustomAttributeDefinitionsRequest
    {
        return CoreHelper::clone($this->instance);
    }
}
