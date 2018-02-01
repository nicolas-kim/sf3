<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Doctrine\DBAL\Connection;
use Tiquette\Domain\Offer;
use Tiquette\Domain\OfferRepository;

class DbalOfferRepository implements OfferRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Offer $offer): void
    {
        $query = <<<SQL
INSERT INTO offers
    (uuid, ticket_uuid, buyer_uuid, proposed_price, price_currency, buyer_message)
VALUES
    (:uuid, :ticket_uuid, :buyer_uuid, :proposedPrice, :priceCurrency, :buyerMessage)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'uuid' => (string) $offer->getId(),
            'ticket_uuid' => (string) $offer->getTicketId(),
            'buyer_uuid' => (string) $offer->getBuyerId(),
            'proposedPrice' => $offer->getProposedPrice()->getAmount(),
            'priceCurrency' => $offer->getProposedPrice()->getCurrency(),
            'buyerMessage' => $offer->getBuyerMessage(),
        ]);
    }

    public function findOffersFromIdTicket($id): array
    {
        $oui = $this->connection->fetchAll("SELECT * FROM offers WHERE ticket_uuid = '$id'");
        return $oui;
    }

    private function hydrateFromRow(array $row): Offer
    {
        return Offer::fromArray($row);
    }
}
