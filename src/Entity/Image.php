<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Image
{
    /**
     * @var null|string
     *
     * @Assert\Url(checkDNS = true)
     * @ORM\Column(name="url", length=255, nullable=true)
     */
    private $url;

    /**
     * @var null|string
     *
     * @ORM\Column(name="extension", length=4, nullable=true)
     */
    private $extension;

    public function __construct(?string $url)
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
