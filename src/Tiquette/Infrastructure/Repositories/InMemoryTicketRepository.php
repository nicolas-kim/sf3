<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Tiquette\Domain\Ticket;
use Tiquette\Domain\TicketId;
use Tiquette\Domain\TicketRepository;

class InMemoryTicketRepository implements TicketRepository
{
    private $tickets = [];

    public function get(TicketId $ticketId): Ticket
    {
        throw new \RuntimeException(sprintf('Method "%s::%s" is not yet implemented', __CLASS__, __METHOD__));
    }

    public function save(Ticket $ticket): void
    {
        $this->tickets[] = $ticket;
    }

    public function findAll(): array
    {
        return $this->tickets;
    }

    public function findLatestSubmittedTickets(): array
    {
        throw new \RuntimeException(sprintf('Method "%s::%s" is not yet implemented', __CLASS__, __METHOD__));
    }

    public function findHotTickets(): array
    {
        throw new \RuntimeException(sprintf('Method "%s::%s" is not yet implemented', __CLASS__, __METHOD__));
    }

    public function findOutdatedTickets(): array
    {
        throw new \RuntimeException(sprintf('Method "%s::%s" is not yet implemented', __CLASS__, __METHOD__));
    }
}
