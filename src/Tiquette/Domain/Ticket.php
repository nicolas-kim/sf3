<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

class Ticket
{
    private $id;
    private $eventName;
    private $eventDate;
    private $eventDescription;
    private $boughtAtPrice;
    private $submittedOn;

    public static function submit(string $eventName, \DateTimeImmutable $eventDate, string $eventDescription,
        Price $boughtAtPrice): self
    {
        return new self(
            TicketId::generate(),
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

    private function __construct(TicketId $id, string $eventName, \DateTimeImmutable $eventDate, string $eventDescription,
        Price $boughtAtPrice, \DateTimeImmutable $submittedOn)
    {
        $this->id = $id;
        $this->eventName = $eventName;
        $this->eventDate = $eventDate;
        $this->eventDescription = $eventDescription;
        $this->boughtAtPrice = $boughtAtPrice;
        $this->submittedOn = $submittedOn;
    }

    /**
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            TicketId::fromString($data['uuid']),
            $data['event_name'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:00', $data['event_date']),
            $data['event_description'],
            Price::inLowestSubunit($data['bought_at_price'], $data['price_currency']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['submitted_on'])
        );
    }
}
