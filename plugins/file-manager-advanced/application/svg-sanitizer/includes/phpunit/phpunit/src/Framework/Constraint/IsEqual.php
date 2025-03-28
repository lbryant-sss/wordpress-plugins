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

use function is_string;
use function sprintf;
use function strpos;
use function trim;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Constraint that checks if one value is equal to another.
 *
 * Equality is checked with PHP's == operator, the operator is explained in
 * detail at {@url https://php.net/manual/en/types.comparisons.php}.
 * Two values are equal if they have the same value disregarding type.
 *
 * The expected value is passed in the constructor.
 */
final class IsEqual extends Constraint
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var float
     */
    private $delta;

    /**
     * @var bool
     */
    private $canonicalize;

    /**
     * @var bool
     */
    private $ignoreCase;

    public function __construct($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false)
    {
        $this->value        = $value;
        $this->delta        = $delta;
        $this->canonicalize = $canonicalize;
        $this->ignoreCase   = $ignoreCase;
    }

    /**
     * Evaluates the constraint for parameter $other.
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @throws ExpectationFailedException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false)
    {
        // If $this->value and $other are identical, they are also equal.
        // This is the most common path and will allow us to skip
        // initialization of all the comparators.
        if ($this->value === $other) {
            return true;
        }

        $comparatorFactory = ComparatorFactory::getInstance();

        try {
            $comparator = $comparatorFactory->getComparatorFor(
                $this->value,
                $other
            );

            $comparator->assertEquals(
                $this->value,
                $other,
                $this->delta,
                $this->canonicalize,
                $this->ignoreCase
            );
        } catch (ComparisonFailure $f) {
            if ($returnResult) {
                return false;
            }

            throw new ExpectationFailedException(
                trim($description . "\n" . $f->getMessage()),
                $f
            );
        }

        return true;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws InvalidArgumentException
     */
    public function toString(): string
    {
        $delta = '';

        if (is_string($this->value)) {
            if (strpos($this->value, "\n") !== false) {
                return 'is equal to <text>';
            }

            return sprintf(
                "is equal to '%s'",
                $this->value
            );
        }

        if ($this->delta != 0) {
            $delta = sprintf(
                ' with delta <%F>',
                $this->delta
            );
        }

        return sprintf(
            'is equal to %s%s',
            $this->exporter()->export($this->value),
            $delta
        );
    }
}
