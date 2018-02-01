<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

class Ticket
{
    private $id;
    private $sellerId;
    private $eventName;
    private $eventDate;
    private $eventDescription;
    private $boughtAtPrice;
    private $submittedOn;
    private $acceptedOfferId;

    public static function submit(MemberId $sellerId, string $eventName, \DateTimeImmutable $eventDate,
        string $eventDescription, Price $boughtAtPrice): self
    {
        return new self(
            TicketId::generate(),
            $sellerId,
            $eventName,
            $eventDate,
            $eventDescription,
            $boughtAtPrice,
            new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
        );
    }

    public function getId(): TicketId
    {
        return $this->id;
    }

    public function getSellerId(): MemberId
    {
        return $this->sellerId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getEventDate(): \DateTimeImmutable
    {
        return $this->eventDate;
    }

    public function getEventDescription(): string
    {
        return $this->eventDescription;
    }

    public function getBoughtAtPrice(): Price
    {
        return $this->boughtAtPrice;
    }

    public function getSubmittedOn(): \DateTimeImmutable
    {
        return $this->submittedOn;
    }

    public function acceptOffer(Offer $offer): void
    {
        if ($this->acceptedOfferId) {

            throw new \DomainException('An offer has already been accepted for this ticket!');
        }

        $this->acceptedOfferId = $offer->getId();
        $offer->accept();
    }

    public function getAcceptedOfferId(): ?OfferId
    {
        return $this->acceptedOfferId;
    }

    private function __construct(TicketId $id, MemberId $sellerId, string $eventName, \DateTimeImmutable $eventDate,
        string $eventDescription, Price $boughtAtPrice, \DateTimeImmutable $submittedOn, ?OfferId $acceptedOfferId = null)
    {
        $this->id = $id;
        $this->sellerId = $sellerId;
        $this->eventName = $eventName;
        $this->eventDate = $eventDate;
        $this->eventDescription = $eventDescription;
        $this->boughtAtPrice = $boughtAtPrice;
        $this->submittedOn = $submittedOn;
        $this->acceptedOfferId = $acceptedOfferId;
    }

    /**
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            TicketId::fromString($data['uuid']),
            MemberId::fromString($data['seller_id']),
            $data['event_name'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:00', $data['event_date']),
            $data['event_description'],
            Price::inLowestSubunit($data['bought_at_price'], $data['price_currency']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['submitted_on']),
            null !== $data['accepted_offer_id']
                ? OfferId::fromString($data['accepted_offer_id'])
                : null
        );
    }
}
