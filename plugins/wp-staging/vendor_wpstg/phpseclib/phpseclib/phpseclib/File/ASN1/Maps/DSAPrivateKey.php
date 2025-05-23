<?php

/**
 * DSAPrivateKey
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace WPStaging\Vendor\phpseclib3\File\ASN1\Maps;

use WPStaging\Vendor\phpseclib3\File\ASN1;
/**
 * DSAPrivateKey
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class DSAPrivateKey
{
    const MAP = ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_SEQUENCE, 'children' => ['version' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER], 'p' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER], 'q' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER], 'g' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER], 'y' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER], 'x' => ['type' => \WPStaging\Vendor\phpseclib3\File\ASN1::TYPE_INTEGER]]];
}
