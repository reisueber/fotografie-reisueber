<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PricePackageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: PricePackageRepository::class)]
#[ORM\Table(name: "app_price_package")]
class PricePackage implements AuditableInterface
{
    use AuditableTrait;

    final public const RESOURCE_KEY = 'price_packages';
    final public const FORM_KEY = 'price_package_details';
    final public const LIST_KEY = 'price_packages';
    final public const SECURITY_CONTEXT = 'app.price_packages';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Serializer\Groups(["Default", "api"])]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    #[Serializer\Groups(["Default", "api"])]
    private string $title;

    #[ORM\ManyToOne(targetEntity: MediaInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Serializer\Groups(["Default", "api"])]
    private ?MediaInterface $image = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    #[Serializer\Groups(["Default", "api"])]
    private ?string $price = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Serializer\Groups(["Default", "api"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, options: ["default" => "[]"])]
    #[Serializer\Groups(["Default", "api"])]
    private array $tracklist = [];

    /**
     * @var array<int, array{text: string}>
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Serializer\Groups(["Default", "api"])]
    private array $bulletPoints = [];

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
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

    /**
     * @return array<int, array{text: string}>
     */
    public function getBulletPoints(): array
    {
        return $this->bulletPoints;
    }

    /**
     * @param array<int, array{text: string}> $bulletPoints
     */
    public function setBulletPoints(array $bulletPoints): void
    {
        $this->bulletPoints = $bulletPoints;
    }
}