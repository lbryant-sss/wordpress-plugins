<?php

/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Entity\Schedule;

use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;

/**
 * Class PeriodService
 *
 * @package AmeliaBooking\Domain\Entity\Schedule
 */
class PeriodService
{
    /** @var Id */
    private $id;

    /** @var Id */
    private $serviceId;

    /**
     * PeriodService constructor.
     *
     * @param Id $serviceId
     */
    public function __construct(
        Id $serviceId
    ) {
        $this->serviceId = $serviceId;
    }

    /**
     * @return Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function setId(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param Id $serviceId
     */
    public function setServiceId(Id $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    public function toArray()
    {
        return [
            'id'        => null !== $this->getId() ? $this->getId()->getValue() : null,
            'serviceId' => $this->serviceId->getValue(),
        ];
    }
}
