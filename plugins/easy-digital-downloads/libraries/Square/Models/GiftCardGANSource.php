<?php

declare(strict_types=1);

namespace EDD\Vendor\Square\Models;

/**
 * Indicates the source that generated the gift card
 * account number (GAN).
 */
class GiftCardGANSource
{
    /**
     * The GAN is generated by Square.
     */
    public const SQUARE = 'SQUARE';

    /**
     * The GAN is provided by a non-EDD\Vendor\Square system. For more information, see
     * [Custom GANs](https://developer.squareup.com/docs/gift-cards/using-gift-cards-api#custom-gans) or
     * [Third-party gift cards](https://developer.squareup.com/docs/gift-cards/using-gift-cards-api#third-
     * party-gift-cards).
     */
    public const OTHER = 'OTHER';
}
