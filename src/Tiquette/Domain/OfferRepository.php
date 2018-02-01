<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

interface OfferRepository
{
    public function save(Offer $offer): void;

    public function get(OfferId $offerId): Offer;

    public function findPendingOffersForMember(MemberId $memberId): array;
}
