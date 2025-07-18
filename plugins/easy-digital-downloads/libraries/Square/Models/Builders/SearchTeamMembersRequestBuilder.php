<?php

declare(strict_types=1);

namespace EDD\Vendor\Square\Models\Builders;

use EDD\Vendor\Core\Utils\CoreHelper;
use EDD\Vendor\Square\Models\SearchTeamMembersQuery;
use EDD\Vendor\Square\Models\SearchTeamMembersRequest;

/**
 * Builder for model SearchTeamMembersRequest
 *
 * @see SearchTeamMembersRequest
 */
class SearchTeamMembersRequestBuilder
{
    /**
     * @var SearchTeamMembersRequest
     */
    private $instance;

    private function __construct(SearchTeamMembersRequest $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Initializes a new Search Team Members Request Builder object.
     */
    public static function init(): self
    {
        return new self(new SearchTeamMembersRequest());
    }

    /**
     * Sets query field.
     *
     * @param SearchTeamMembersQuery|null $value
     */
    public function query(?SearchTeamMembersQuery $value): self
    {
        $this->instance->setQuery($value);
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
     * Initializes a new Search Team Members Request object.
     */
    public function build(): SearchTeamMembersRequest
    {
        return CoreHelper::clone($this->instance);
    }
}
