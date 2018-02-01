<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Utils;

use AppBundle\Forms\TicketSubmission;
use Tiquette\Domain\Price;
use Tiquette\Domain\Ticket;

class TicketFactory
{
    public function fromTicketSubmission(TicketSubmission $ticketSubmission): Ticket
    {
        return Ticket::submit(
            $ticketSubmission->eventName,
            \DateTimeImmutable::createFromMutable($ticketSubmission->eventDate),
            $ticketSubmission->eventDescription,
            Price::inLowestSubunit($ticketSubmission->boughtAtPrice, 'EUR')
        );
    }
}
