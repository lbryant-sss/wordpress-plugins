<?php

declare(strict_types=1);


namespace WpRollback\Free\Dependencies\StellarWP\AdminNotices;

use WpRollback\Free\Dependencies\StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\Exceptions\NotificationCollisionException;

class NotificationsRegistrar implements NotificationsRegistrarInterface
{
    protected $notices = [];

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    public function registerNotice(AdminNotice $notice): void
    {
        $id = $notice->getId();

        if (isset($this->notices[$id])) {
            throw new NotificationCollisionException($id, $notice);
        }

        $this->notices[$id] = $notice;
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    public function unregisterNotice(string $id): void
    {
        unset($this->notices[$id]);
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    public function getNotices(): array
    {
        return array_values($this->notices);
    }
}
