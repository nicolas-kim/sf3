<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Doctrine\DBAL\Connection;
use Tiquette\Domain\Price;
use Tiquette\Domain\Ticket;
use Tiquette\Domain\TicketId;
use Tiquette\Domain\TicketNotFound;
use Tiquette\Domain\TicketRepository;
use Tiquette\Domain\ViewModels\TicketDetails;
use Tiquette\Domain\ViewModels\TicketSummary;

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
            'uuid' => $ticket->getId(),
            'event_name' => $ticket->getEventName(),
            'event_description' => $ticket->getEventDescription(),
            'event_date' => $ticket->getEventDate()->format('Y-m-d\TH:i:00'),
            'bought_at_price' => $ticket->getBoughtAtPrice()->getAmount(),
            'price_currency' => $ticket->getBoughtAtPrice()->getCurrency(),
            'submitted_on' => $ticket->getSubmittedOn()->format('Y-m-d\TH:i:s'),
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

    /** @return TicketSummary[] */
    public function findLatestSubmittedTickets(): array
    {
        $query =<<<SQL
SELECT * FROM tickets t
LEFT JOIN (
	SELECT t.uuid, IFNULL(COUNT(ticket_uuid), 0) AS offer_count FROM tickets t
	LEFT JOIN offers o ON t.uuid = o.ticket_uuid
	GROUP BY t.uuid
) tmp ON tmp.uuid = t.uuid
WHERE event_date >= NOW()
ORDER BY submitted_on DESC;
SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute();

        $ticketSummaries = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

            $ticketSummary = new TicketSummary();
            $ticketSummary->ticketId = $row['uuid'];
            $ticketSummary->boughtAtPrice = Price::inLowestSubunit($row['bought_at_price'], $row['price_currency']);
            $ticketSummary->eventDate = $row['event_date'];
            $ticketSummary->eventName = $row['event_name'];
            $ticketSummary->numberOfOffersMade = (int) $row['offer_count'];
            $ticketSummaries[] = $ticketSummary;
        }

        return  $ticketSummaries;
    }

    public function findHotTickets(): array
    {
        $query =<<<SQL
SELECT * FROM tickets t
LEFT JOIN (
	SELECT t.uuid, IFNULL(COUNT(ticket_uuid), 0) AS offer_count FROM tickets t
	LEFT JOIN offers o ON t.uuid = o.ticket_uuid
	GROUP BY t.uuid
) tmp ON tmp.uuid = t.uuid
WHERE event_date >= NOW()
ORDER BY offer_count DESC;
SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute();

        $ticketSummaries = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

            $ticketSummary = new TicketSummary();
            $ticketSummary->ticketId = $row['uuid'];
            $ticketSummary->boughtAtPrice = Price::inLowestSubunit($row['bought_at_price'], $row['price_currency']);
            $ticketSummary->eventDate = $row['event_date'];
            $ticketSummary->eventName = $row['event_name'];
            $ticketSummary->numberOfOffersMade = (int) $row['offer_count'];
            $ticketSummaries[] = $ticketSummary;
        }

        return  $ticketSummaries;
    }

    public function getTicketDetails(TicketId $ticketId): TicketDetails
    {
        $query =<<<SQL
SELECT * FROM tickets t
LEFT JOIN (
	SELECT t.uuid, IFNULL(COUNT(ticket_uuid), 0) AS offer_count FROM tickets t
	LEFT JOIN offers o ON t.uuid = o.ticket_uuid
	GROUP BY t.uuid
) tmp ON tmp.uuid = t.uuid
WHERE t.uuid = :ticketId;
SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute(['ticketId' => (string) $ticketId]);

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {

            throw TicketNotFound::unknownId($ticketId);
        }

        $ticketDetail = new TicketDetails();
        $ticketDetail->ticketId = $row['uuid'];
        $ticketDetail->boughtAtPrice = Price::inLowestSubunit($row['bought_at_price'], $row['price_currency']);
        $ticketDetail->eventDate = $row['event_date'];
        $ticketDetail->eventDescription = $row['event_description'];
        $ticketDetail->eventName = $row['event_name'];
        $ticketDetail->numberOfOffersMade = (int) $row['offer_count'];

        return  $ticketDetail;
    }

    private function hydrateFromRow(array $row): Ticket
    {
        return Ticket::fromArray($row);
    }
}
