<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/albums', name: 'albums')]
    public function index(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();

        $albumsPos = [];

        foreach ($albums as $album) {
            $artist = $album->getArtist();
            $artistAlbums = $albumRepository->findBy(['artist' => $artist], ['id' => 'ASC']);
            $albumPosition = array_search($album, $artistAlbums, true);

            if ($albumPosition !== false) {
                $albumsPos[$album->getId()] = $albumPosition + 1;
            }
        }

        return $this->render('albums.html.twig', [
            'albums' => $albums,
            'albumsPos' => $albumsPos,
        ]);
    }

    #[Route('/artist/{artistId}/album/{albumPos}', name: 'album_detail', methods: ['GET'])]
    public function albumDetail(int $artistId, int $albumPos, ArtistRepository $artistRepository, AlbumRepository $albumRepository): Response
    {
        $artist = $artistRepository->find($artistId);

        if (!$artist) {
            throw $this->createNotFoundException('Artist not found');
        }

        $albums = $albumRepository->findBy(['artist' => $artist]);

        usort($albums, fn($a, $b) => $a->getId() <=> $b->getId());

        if ($albumPos < 1 || $albumPos > count($albums)) {
            throw $this->createNotFoundException('Album position out of range');
        }

        $album = $albums[$albumPos - 1];

        return $this->render('albumShow.html.twig', [
            'albumPos' => $albumPos,
            'album' => $album,
        ]);
    }
}
