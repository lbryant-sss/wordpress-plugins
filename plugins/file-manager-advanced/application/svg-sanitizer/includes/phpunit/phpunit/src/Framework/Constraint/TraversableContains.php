<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use function is_array;
use function is_object;
use function is_string;
use function sprintf;
use function strpos;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use SplObjectStorage;

/**
 * Constraint that asserts that the Traversable it is applied to contains
 * a given value.
 *
 * @deprecated Use TraversableContainsEqual or TraversableContainsIdentical instead
 */
final class TraversableContains extends Constraint
{
    /**
     * @var bool
     */
    private $checkForObjectIdentity;

    /**
     * @var bool
     */
    private $checkForNonObjectIdentity;

    /**
     * @var mixed
     */
    private $value;

    public function __construct($value, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false)
    {
        $this->checkForObjectIdentity    = $checkForObjectIdentity;
        $this->checkForNonObjectIdentity = $checkForNonObjectIdentity;
        $this->value                     = $value;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws InvalidArgumentException
     */
    public function toString(): string
    {
        if (is_string($this->value) && strpos($this->value, "\n") !== false) {
            return 'contains "' . $this->value . '"';
        }

        return 'contains ' . $this->exporter()->export($this->value);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other): bool
    {
        if ($other instanceof SplObjectStorage) {
            return $other->contains($this->value);
        }

        if (is_object($this->value)) {
            foreach ($other as $element) {
                if ($this->checkForObjectIdentity && $element === $this->value) {
                    return true;
                }

                /* @noinspection TypeUnsafeComparisonInspection */
                if (!$this->checkForObjectIdentity && $element == $this->value) {
                    return true;
                }
            }
        } else {
            foreach ($other as $element) {
                if ($this->checkForNonObjectIdentity && $element === $this->value) {
                    return true;
                }

                /* @noinspection TypeUnsafeComparisonInspection */
                if (!$this->checkForNonObjectIdentity && $element == $this->value) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return sprintf(
            '%s %s',
            is_array($other) ? 'an array' : 'a traversable',
            $this->toString()
        );
    }
}
