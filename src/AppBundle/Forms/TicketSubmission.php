<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;

class TicketSubmission
{
    /** @Assert\NotBlank */
    public $eventName;

    /** @Assert\DateTime(format="Y-m-d\TH:i") */
    /** @Assert\NotBlank */
    public $eventDate;

    /** @Assert\NotBlank */
    public $eventDescription;

    public $boughtAtPrice;
}
