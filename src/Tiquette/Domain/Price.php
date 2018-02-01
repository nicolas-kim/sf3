<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Domain;

class Price
{
    private $amount;
    private $currency;

    /**
     * Create a price using the lowest subunit in the given currency
     * For example, to set a price of 10 EUR, you must provide an amount of 1000
     */
    public static function inLowestSubunit(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    private function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }
}
