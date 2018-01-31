<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;

class OfferSubmission
{
    /** @Assert\NotBlank */
    public $offerPrice;

    /** @Assert\NotBlank */
    public $offerText;
}
