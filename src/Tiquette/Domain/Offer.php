<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

class Offer
{
    private $id;
    private $ticketId;
    private $buyerId;
    private $proposedPrice;
    private $buyerMessage;
    private $acceptedOn;

    public static function for(TicketId $ticketId, MemberId $buyerId,
        Price $proposedPrice, string $buyerMessage): self
    {
        return new self(OfferId::generate(), $ticketId, $buyerId, $proposedPrice, $buyerMessage);
    }

    private function __construct(OfferId $id, TicketId $ticketId, MemberId $buyerId,
        Price $proposedPrice, string $buyerMessage, ?\DateTimeImmutable $acceptedOn = null)
    {
        $this->id = $id;
        $this->ticketId = $ticketId;
        $this->buyerId = $buyerId;
        $this->proposedPrice = $proposedPrice;
        $this->buyerMessage = $buyerMessage;
        $this->acceptedOn = $acceptedOn;
    }

    public function getId(): OfferId
    {
        return $this->id;
    }

    public function getAcceptedOn(): ?\DateTimeImmutable
    {
        return $this->acceptedOn;
    }

    public function getTicketId(): TicketId
    {
        return $this->ticketId;
    }

    public function getBuyerId(): MemberId
    {
        return $this->buyerId;
    }

    public function getProposedPrice(): Price
    {
        return $this->proposedPrice;
    }

    public function getBuyerMessage(): string
    {
        return $this->buyerMessage;
    }

    public function hasBeenAccepted(): bool
    {
        return (bool) $this->acceptedOn;
    }

    public function accept(): void
    {
        $this->acceptedOn = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public static function fromArray(array $data): self
    {
        return new self(
            OfferId::fromString($data['uuid']),
            TicketId::fromString($data['ticket_uuid']),
            MemberId::fromString($data['buyer_uuid']),
            Price::inLowestSubunit($data['proposed_price'], $data['price_currency']),
            $data['buyer_message'],
            $data['accepted_on'] !== null
                ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['accepted_on'])
                : null
        );
    }
}
