<?php

/**
 * Curves over a*x^2 + y^2 = 1 + d*x^2*y^2
 *
 * http://www.secg.org/SEC2-Ver-1.0.pdf provides for curves with custom parameters.
 * ie. the coefficients can be arbitrary set through specially formatted keys, etc.
 * As such, Prime.php is built very generically and it's not able to take full
 * advantage of curves with 0 coefficients to produce simplified point doubling,
 * point addition. Twisted Edwards curves, in contrast, do not have a way, currently,
 * to customize them. As such, we can omit the super generic stuff from this class
 * and let the named curves (Ed25519 and Ed448) define their own custom tailored
 * point addition and point doubling methods.
 *
 * More info:
 *
 * https://en.wikipedia.org/wiki/Twisted_Edwards_curve
 *
 * PHP version 5 and 7
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2017 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/Math_BigInteger
 */
namespace WPStaging\Vendor\phpseclib3\Crypt\EC\BaseCurves;

use WPStaging\Vendor\phpseclib3\Math\BigInteger;
use WPStaging\Vendor\phpseclib3\Math\PrimeField;
use WPStaging\Vendor\phpseclib3\Math\PrimeField\Integer as PrimeInteger;
/**
 * Curves over a*x^2 + y^2 = 1 + d*x^2*y^2
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
class TwistedEdwards extends \WPStaging\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base
{
    /**
     * The modulo
     *
     * @var BigInteger
     */
    protected $modulo;
    /**
     * Cofficient for x^2
     *
     * @var object
     */
    protected $a;
    /**
     * Cofficient for x^2*y^2
     *
     * @var object
     */
    protected $d;
    /**
     * Base Point
     *
     * @var object[]
     */
    protected $p;
    /**
     * The number zero over the specified finite field
     *
     * @var object
     */
    protected $zero;
    /**
     * The number one over the specified finite field
     *
     * @var object
     */
    protected $one;
    /**
     * The number two over the specified finite field
     *
     * @var object
     */
    protected $two;
    /**
     * Sets the modulo
     */
    public function setModulo(\WPStaging\Vendor\phpseclib3\Math\BigInteger $modulo)
    {
        $this->modulo = $modulo;
        $this->factory = new \WPStaging\Vendor\phpseclib3\Math\PrimeField($modulo);
        $this->zero = $this->factory->newInteger(new \WPStaging\Vendor\phpseclib3\Math\BigInteger(0));
        $this->one = $this->factory->newInteger(new \WPStaging\Vendor\phpseclib3\Math\BigInteger(1));
        $this->two = $this->factory->newInteger(new \WPStaging\Vendor\phpseclib3\Math\BigInteger(2));
    }
    /**
     * Set coefficients a and b
     */
    public function setCoefficients(\WPStaging\Vendor\phpseclib3\Math\BigInteger $a, \WPStaging\Vendor\phpseclib3\Math\BigInteger $d)
    {
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        $this->a = $this->factory->newInteger($a);
        $this->d = $this->factory->newInteger($d);
    }
    /**
     * Set x and y coordinates for the base point
     */
    public function setBasePoint($x, $y)
    {
        switch (\true) {
            case !$x instanceof \WPStaging\Vendor\phpseclib3\Math\BigInteger && !$x instanceof \WPStaging\Vendor\phpseclib3\Math\PrimeField\Integer:
                throw new \UnexpectedValueException('WPStaging\\Vendor\\Argument 1 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\\Integer');
            case !$y instanceof \WPStaging\Vendor\phpseclib3\Math\BigInteger && !$y instanceof \WPStaging\Vendor\phpseclib3\Math\PrimeField\Integer:
                throw new \UnexpectedValueException('WPStaging\\Vendor\\Argument 2 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\\Integer');
        }
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        $this->p = [$x instanceof \WPStaging\Vendor\phpseclib3\Math\BigInteger ? $this->factory->newInteger($x) : $x, $y instanceof \WPStaging\Vendor\phpseclib3\Math\BigInteger ? $this->factory->newInteger($y) : $y];
    }
    /**
     * Returns the a coefficient
     *
     * @return PrimeInteger
     */
    public function getA()
    {
        return $this->a;
    }
    /**
     * Returns the a coefficient
     *
     * @return PrimeInteger
     */
    public function getD()
    {
        return $this->d;
    }
    /**
     * Retrieve the base point as an array
     *
     * @return array
     */
    public function getBasePoint()
    {
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        /*
        if (!isset($this->p)) {
            throw new \RuntimeException('setBasePoint needs to be called before this method');
        }
        */
        return $this->p;
    }
    /**
     * Returns the affine point
     *
     * @return PrimeField\Integer[]
     */
    public function convertToAffine(array $p)
    {
        if (!isset($p[2])) {
            return $p;
        }
        list($x, $y, $z) = $p;
        $z = $this->one->divide($z);
        return [$x->multiply($z), $y->multiply($z)];
    }
    /**
     * Returns the modulo
     *
     * @return BigInteger
     */
    public function getModulo()
    {
        return $this->modulo;
    }
    /**
     * Tests whether or not the x / y values satisfy the equation
     *
     * @return boolean
     */
    public function verifyPoint(array $p)
    {
        list($x, $y) = $p;
        $x2 = $x->multiply($x);
        $y2 = $y->multiply($y);
        $lhs = $this->a->multiply($x2)->add($y2);
        $rhs = $this->d->multiply($x2)->multiply($y2)->add($this->one);
        return $lhs->equals($rhs);
    }
}
