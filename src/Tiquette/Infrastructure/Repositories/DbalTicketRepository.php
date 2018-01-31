<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Doctrine\DBAL\Connection;
use Tiquette\Domain\Ticket;
use Tiquette\Domain\TicketRepository;

class DbalTicketRepository implements TicketRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Ticket $ticket): void
    {
        $data = [
            'event_name' => $ticket->getEventName(),
            'event_description' => $ticket->getEventDescription(),
            'event_date' => $ticket->getEventDate()->format('Y-m-d\TH:i:00'),
            'bought_at_price' => $ticket->getBoughtAtPrice(),
            'price_currency' => 'EUR',
        ];

        $this->connection->insert('tickets', $data);
    }

    public function findAll(): array
    {
        $query =<<<SQL
SELECT * FROM tickets;
SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute();

        $tickets = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

            $tickets[] = $this->hydrateFromRow($row);
        }

        return $tickets;
    }

    public function findBuyableTickets(): array
    {
        $query =<<<SQL
SELECT * FROM tickets WHERE ;
SQL;
        $statement = $this->connection->prepare($query);
        $statement->execute();

        $tickets = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

            $tickets[] = $this->hydrateFromRow($row);
        }

        return $tickets;
    }

    public function findTicketFromName($name): array
    {
        $query=<<<SQL
SELECT * FROM tickets WHERE event_name = "$name";
SQL;
        $statement = $this->connection->prepare($query);
        $statement->execute();

        $ticket = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

            $ticket[] = $this->hydrateFromRow($row);
        }

        return $ticket;

    }

    private function hydrateFromRow(array $row): Ticket
    {
        return Ticket::fromArray($row);
    }
}
