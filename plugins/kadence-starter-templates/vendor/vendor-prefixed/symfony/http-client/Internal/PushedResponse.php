<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Internal;

use KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Response\CurlResponse;

/**
 * A pushed response with its request headers.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 *
 * @internal
 */
final class PushedResponse
{
    public $response;

    /** @var string[] */
    public $requestHeaders;

    public $parentOptions = [];

    public $handle;

    public function __construct(CurlResponse $response, array $requestHeaders, array $parentOptions, $handle)
    {
        $this->response = $response;
        $this->requestHeaders = $requestHeaders;
        $this->parentOptions = $parentOptions;
        $this->handle = $handle;
    }
}
