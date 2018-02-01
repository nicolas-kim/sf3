<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

class OffersService
{
    private $ticketRepository;
    private $offerRepository;

    public function __construct(TicketRepository $ticketRepository, OfferRepository $offerRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->offerRepository = $offerRepository;
    }

    public function acceptOffer(OfferId $offerId): void
    {
        $offer = $this->offerRepository->get($offerId);
        $ticket = $this->ticketRepository->get($offer->getTicketId());

        $ticket->acceptOffer($offer);

        $this->ticketRepository->save($ticket);
        $this->offerRepository->save($offer);
    }
}
