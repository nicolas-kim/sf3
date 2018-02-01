<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Utils;

use AppBundle\Forms\TicketSubmission;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tiquette\Domain\Price;
use Tiquette\Domain\Ticket;

class TicketFactory
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function fromTicketSubmission(TicketSubmission $ticketSubmission): Ticket
    {
        $sellerId = $this->tokenStorage->getToken()->getUser()->getId();

        return Ticket::submit(
            $sellerId,
            $ticketSubmission->eventName,
            \DateTimeImmutable::createFromMutable($ticketSubmission->eventDate),
            $ticketSubmission->eventDescription,
            Price::inLowestSubunit($ticketSubmission->boughtAtPrice, 'EUR')
        );
    }
}
