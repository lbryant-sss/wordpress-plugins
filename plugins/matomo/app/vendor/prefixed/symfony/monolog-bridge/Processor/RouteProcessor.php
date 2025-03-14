<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Matomo\Dependencies\Symfony\Bridge\Monolog\Processor;

use Matomo\Dependencies\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Matomo\Dependencies\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Matomo\Dependencies\Symfony\Component\HttpKernel\Event\RequestEvent;
use Matomo\Dependencies\Symfony\Component\HttpKernel\KernelEvents;
use Matomo\Dependencies\Symfony\Contracts\Service\ResetInterface;
/**
 * Adds the current route information to the log entry.
 *
 * @author Piotr Stankowski <git@trakos.pl>
 *
 * @final
 */
class RouteProcessor implements EventSubscriberInterface, ResetInterface
{
    private $routeData;
    private $includeParams;
    public function __construct(bool $includeParams = \true)
    {
        $this->includeParams = $includeParams;
        $this->reset();
    }
    public function __invoke(array $records) : array
    {
        if ($this->routeData && !isset($records['extra']['requests'])) {
            $records['extra']['requests'] = array_values($this->routeData);
        }
        return $records;
    }
    public function reset()
    {
        $this->routeData = [];
    }
    public function addRouteData(RequestEvent $event)
    {
        if ($event->isMainRequest()) {
            $this->reset();
        }
        $request = $event->getRequest();
        if (!$request->attributes->has('_controller')) {
            return;
        }
        $currentRequestData = ['controller' => $request->attributes->get('_controller'), 'route' => $request->attributes->get('_route')];
        if ($this->includeParams) {
            $currentRequestData['route_params'] = $request->attributes->get('_route_params');
        }
        $this->routeData[spl_object_id($request)] = $currentRequestData;
    }
    public function removeRouteData(FinishRequestEvent $event)
    {
        $requestId = spl_object_id($event->getRequest());
        unset($this->routeData[$requestId]);
    }
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::REQUEST => ['addRouteData', 1], KernelEvents::FINISH_REQUEST => ['removeRouteData', 1]];
    }
}
