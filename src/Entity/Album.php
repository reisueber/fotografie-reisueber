<?php

// src/Entity/Album.php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity]
#[ORM\Table(name: "app_album")]
class Album implements AuditableInterface
{
    use AuditableTrait;

    final public const RESOURCE_KEY = 'albums';
    final public const FORM_KEY = 'album_details';
    final public const LIST_KEY = 'albums';
    final public const SECURITY_CONTEXT = 'sulu.album.albums';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    private string $title;

    #[ORM\ManyToOne(targetEntity: MediaInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?MediaInterface $image = null;

    /**
     * @var mixed[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $tracklist = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }

    public function setImage(?MediaInterface $image): void
    {
        $this->image = $image;
    }

    /**
     * @return array<int, array{title: string, interpreter?: string|null}>
     */
    public function getTracklist(): array
    {
        return $this->tracklist;
    }

    /**
     * @param array<int, array{title: string, interpreter?: string|null}> $tracklist
     */
    public function setTracklist(array $tracklist): void
    {
        $this->tracklist = $tracklist;
    }
}