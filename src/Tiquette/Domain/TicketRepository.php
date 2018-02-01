<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

interface TicketRepository
{
    public function save(Ticket $ticket): void;

    public function get(TicketId $ticketId): Ticket;

    /** @return Ticket[] */
    public function findAll(): array;

    public function findLatestSubmittedTickets(): array;
    public function findHotTickets(): array;
}
