<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Doctrine\DBAL\Connection;
use Tiquette\Domain\MemberId;
use Tiquette\Domain\Offer;
use Tiquette\Domain\OfferId;
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
ON DUPLICATE KEY
    UPDATE
      accepted_on = :accepted_on
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
            'accepted_on' => $offer->getAcceptedOn()
                ? $offer->getAcceptedOn()->format(DATE_ATOM)
                : null
        ]);
    }

    public function get(OfferId $offerId): Offer
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from('offers')
            ->where($qb->expr()->like('uuid', ':offerId'))
            ->setMaxResults(1)
            ->setParameters(['offerId' => (string) $offerId])
        ;

        $stmt = $qb->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function findPendingOffersForMember(MemberId $memberId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('o.*')
            ->from('offers', 'o')
            ->innerJoin('o', 'tickets', 't', 't.uuid = o.ticket_uuid')
            ->where($qb->expr()->like('t.seller_id', ':sellerId'))
            ->andWhere($qb->expr()->isNull('o.accepted_on'))
            ->setParameters(['sellerId' => (string) $memberId])
        ;

        $stmt = $qb->execute();

        $offers = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $offers[] = $this->hydrateFromRow($row);
        }

        return $offers;
    }

    private function hydrateFromRow(array $row): Offer
    {
        return Offer::fromArray($row);
    }
}
