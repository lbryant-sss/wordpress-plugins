<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Matomo\Dependencies\Symfony\Bridge\Monolog\Handler;

use Matomo\Dependencies\Monolog\Handler\SwiftMailerHandler as BaseSwiftMailerHandler;
use Matomo\Dependencies\Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Matomo\Dependencies\Symfony\Component\HttpKernel\Event\TerminateEvent;
trigger_deprecation('symfony/monolog-bridge', '5.4', '"%s" is deprecated and will be removed in 6.0.', SwiftMailerHandler::class);
/**
 * Extended SwiftMailerHandler that flushes mail queue if necessary.
 *
 * @author Philipp Kräutli <pkraeutli@astina.ch>
 *
 * @final
 *
 * @deprecated since Symfony 5.4
 */
class SwiftMailerHandler extends BaseSwiftMailerHandler
{
    protected $transport;
    protected $instantFlush = \false;
    public function setTransport(\Matomo\Dependencies\Swift_Transport $transport)
    {
        $this->transport = $transport;
    }
    /**
     * After the kernel has been terminated we will always flush messages.
     */
    public function onKernelTerminate(TerminateEvent $event)
    {
        $this->instantFlush = \true;
    }
    /**
     * After the CLI application has been terminated we will always flush messages.
     */
    public function onCliTerminate(ConsoleTerminateEvent $event)
    {
        $this->instantFlush = \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records) : void
    {
        parent::send($content, $records);
        if ($this->instantFlush) {
            $this->flushMemorySpool();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function reset() : void
    {
        $this->flushMemorySpool();
    }
    /**
     * Flushes the mail queue if a memory spool is used.
     */
    private function flushMemorySpool()
    {
        $mailerTransport = $this->mailer->getTransport();
        if (!$mailerTransport instanceof \Matomo\Dependencies\Swift_Transport_SpoolTransport) {
            return;
        }
        $spool = $mailerTransport->getSpool();
        if (!$spool instanceof \Matomo\Dependencies\Swift_MemorySpool) {
            return;
        }
        if (null === $this->transport) {
            throw new \Exception('No transport available to flush mail queue.');
        }
        $spool->flushQueue($this->transport);
    }
}
