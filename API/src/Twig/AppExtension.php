<?php

namespace App\Twig;

use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use App\Repository\SongRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private ArtistRepository $artistRepository;
    private AlbumRepository $albumRepository;
    private SongRepository $songRepository;

    public function __construct(ArtistRepository $artistRepository, AlbumRepository $albumRepository, SongRepository $songRepository)
    {
        $this->artistRepository = $artistRepository;
        $this->albumRepository = $albumRepository;
        $this->songRepository = $songRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_artist_count', [$this, 'getArtistCount']),
            new TwigFunction('get_album_count', [$this, 'getAlbumCount']),
            new TwigFunction('get_song_count', [$this, 'getSongCount']),
        ];
    }

    public function getArtistCount(): int
    {
        return $this->artistRepository->count([]);
    }

    public function getAlbumCount(): int
    {
        return $this->albumRepository->count([]);
    }

    public function getSongCount(): int
    {
        return $this->songRepository->count([]);
    }
}
