<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice", indexes={@ORM\Index(name="FK_sender", columns={"sender"}), @ORM\Index(name="FK_currency", columns={"currency"}), @ORM\Index(name="FK_recipient", columns={"recipient"})})
 * @ORM\Entity
 */
class Invoice
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
     * @ORM\Column(name="value", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $currency;

    /**
     * @var int
     *
     * @ORM\Column(name="sender", type="integer", nullable=false)
     */
    private $sender;

    /**
     * @var int
     *
     * @ORM\Column(name="recipient", type="integer", nullable=false)
     */
    private $recipient;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getSender(): ?int
    {
        return $this->sender;
    }

    public function setSender(int $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?int
    {
        return $this->recipient;
    }

    public function setRecipient(int $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }


}
