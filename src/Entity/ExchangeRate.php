<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExchangeRate
 *
 * @ORM\Table(name="exchange_rate", uniqueConstraints={@ORM\UniqueConstraint(name="date", columns={"date", "currency"})})
 * @ORM\Entity
 */
class ExchangeRate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(name="rate_to_PLN", type="float", precision=10, scale=0, nullable=false)
     */
    private $rateToPLN;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRateToPLN(): ?float
    {
        return $this->rateToPLN;
    }

    public function setRateToPLN(float $rateToPln): self
    {
        $this->rateToPLN = $rateToPln;

        return $this;
    }


}
