<?php

namespace WOE\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;

use WOE\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;
class BSE
{
    const BLIPTYPE_ERROR = 0x0;
    const BLIPTYPE_UNKNOWN = 0x1;
    const BLIPTYPE_EMF = 0x2;
    const BLIPTYPE_WMF = 0x3;
    const BLIPTYPE_PICT = 0x4;
    const BLIPTYPE_JPEG = 0x5;
    const BLIPTYPE_PNG = 0x6;
    const BLIPTYPE_DIB = 0x7;
    const BLIPTYPE_TIFF = 0x11;
    const BLIPTYPE_CMYKJPEG = 0x12;
    /**
     * The parent BLIP Store Entry Container.
     * Property is never currently read.
     *
     * @var BstoreContainer
     */
    private $parent;
    // @phpstan-ignore-line
    /**
     * The BLIP (Big Large Image or Picture).
     *
     * @var ?BSE\Blip
     */
    private $blip;
    /**
     * The BLIP type.
     *
     * @var int
     */
    private $blipType;
    /**
     * Set parent BLIP Store Entry Container.
     */
    public function setParent(BstoreContainer $parent): void
    {
        $this->parent = $parent;
    }
    /**
     * Get the BLIP.
     *
     * @return ?BSE\Blip
     */
    public function getBlip()
    {
        return $this->blip;
    }
    /**
     * Set the BLIP.
     */
    public function setBlip(\WOE\PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE\Blip $blip): void
    {
        $this->blip = $blip;
        $blip->setParent($this);
    }
    /**
     * Get the BLIP type.
     *
     * @return int
     */
    public function getBlipType()
    {
        return $this->blipType;
    }
    /**
     * Set the BLIP type.
     *
     * @param int $blipType
     */
    public function setBlipType($blipType): void
    {
        $this->blipType = $blipType;
    }
}