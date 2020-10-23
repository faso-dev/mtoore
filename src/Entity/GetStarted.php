<?php

namespace App\Entity;

use App\Repository\GetStartedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GetStartedRepository::class)
 */
class GetStarted
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $welcome;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWelcome(): ?string
    {
        return $this->welcome;
    }

    public function setWelcome(string $welcome): self
    {
        $this->welcome = $welcome;

        return $this;
    }
}
