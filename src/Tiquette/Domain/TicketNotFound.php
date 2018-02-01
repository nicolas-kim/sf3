<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

use Tiquette\Exception;

class TicketNotFound extends \DomainException implements Exception
{
    public static function unknownId(TicketId $id): self
    {
        return new self(sprintf('Ticket with id "%s" not found.', $id));
    }
}
