<?php

namespace DevOwl\RealCookieBanner\Vendor\DevOwl\FastHtmlTag\finder;

use DevOwl\RealCookieBanner\Vendor\DevOwl\FastHtmlTag\FastHtmlTag;
use DevOwl\RealCookieBanner\Vendor\DevOwl\FastHtmlTag\finder\match\AbstractMatch;
/**
 * A finder describes a schema parsing a HTML string, and run a registered callback
 * when a match got found.
 * @internal
 */
abstract class AbstractFinder
{
    /**
     * The current `FastHtmlTag` instance doing the replace.
     *
     * @var FastHtmlTag
     */
    private $fastHtmlTag = null;
    /**
     * Callbacks.
     *
     * @var callable[]
     */
    private $callbacks = [];
    /**
     * Find HTML tags for this finder and replace it with our modifiers.
     *
     * @param string $html
     */
    public abstract function replace($html);
    /**
     * Add a callable. The first parameter is an instance of `AbstractMatch`.
     *
     * @param callable $callback
     */
    public function addCallback($callback)
    {
        $this->callbacks[] = $callback;
    }
    /**
     * Apply registered callbacks to our match.
     *
     * @param AbstractMatch|false|string $match
     */
    protected function applyCallbacks($match)
    {
        if ($match === \false || \is_string($match)) {
            return;
        }
        foreach ($this->callbacks as $callback) {
            $callback($match);
        }
    }
    /**
     * Setter.
     *
     * @param FastHtmlTag $fastHtmlTag
     */
    public function setFastHtmlTag($fastHtmlTag)
    {
        $this->fastHtmlTag = $fastHtmlTag;
    }
    /**
     * Getter.
     *
     * @codeCoverageIgnore
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }
    /**
     * Getter.
     */
    public function getFastHtmlTag()
    {
        return $this->fastHtmlTag;
    }
}
