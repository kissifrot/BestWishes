<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Image
{
    #[Assert\Url]
    #[ORM\Column(name: 'url', length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(name: 'extension', length: 4, nullable: true)]
    private ?string $extension = null;

    public function __construct(?string $url)
    {
        $this->url = $url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }
}
