<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

interface OfferRepository
{
    public function save(Offer $offer): void;
}
