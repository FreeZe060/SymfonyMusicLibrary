<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SongRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Metadata\Get;

#[ORM\Entity(repositoryClass: SongRepository::class)]
// #[ApiResource(
//     normalizationContext: ['groups' => ['song:read']],
//     denormalizationContext: ['groups' => ['song:write']],
//     operations: [
//         new Get(
//             uriTemplate: '/artists/{artistId<\d+>}/albums/{albumPos<\d+>}/songs/{songPos<\d+>}',
//             name: 'song_detailAPI',
//         ),
//     ]
// )]
#[ApiResource()]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['song:read', 'song:write'])]
    private $title;

    #[ORM\Column(type: 'string', length: 124)]
    #[Groups(['song:read', 'song:write'])]
    private $genre;

    #[ORM\Column(type: 'integer')]
    #[Groups(['song:read', 'song:write'])]
    private $length;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['song:read'])]
    #[MaxDepth(1)]
    private ?Album $album = null;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }
}
