<?php

namespace App\Controller;

use App\Repository\SongRepository;
use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SongController extends AbstractController
{
    #[Route('/songs', name: 'songs', methods: ['GET'])]
    public function index(SongRepository $songRepository, AlbumRepository $albumRepository, ArtistRepository $artistRepository, Request $request): Response
    {
        $query = $request->query->get('query', '');
        $selectedGenre = $request->query->get('genre', '');

        $criteria = [];
        $criteria['title'] = '';
        $criteria['genre'] = '';
        
        if (!empty($query)) {
            $criteria['title'] = $query;
        }
        if (!empty($selectedGenre)) {
            $criteria['genre'] = $selectedGenre;
        }

        $songs = $songRepository->findAll();

        $albumsPos = [];
        $songsPos = [];
        $genres = [];

        foreach ($songs as $song) {
            if (!in_array($song->getGenre(), $genres)) {
                $genres[] = $song->getGenre();
            }

            $artist = $song->getAlbum()->getArtist();
            $albums = $albumRepository->findBy(['artist' => $artist], ['id' => 'ASC']);
            $albumPosition = array_search($song->getAlbum(), $albums, true);

            if ($albumPosition !== false) {
                $albumsPos[$song->getAlbum()->getId()] = $albumPosition + 1;
            }

            $songsInAlbum = $songRepository->findBy(['album' => $song->getAlbum()], ['id' => 'ASC']);
            $songPosition = array_search($song, $songsInAlbum, true);

            if ($songPosition !== false) {
                $songsPos[$song->getId()] = $songPosition + 1;
            }
        }

        return $this->render('songs.html.twig', [
            'songs' => $songs,
            'genres' => $genres,
            'albumsPos' => $albumsPos,
            'songsPos' => $songsPos,
            'criteria' => $criteria,
        ]);
    }

    #[Route('/artist/{artistId}/album/{albumPos}/song/{songPos}', name: 'song_detail', methods: ['GET'])]
    public function songDetail(int $artistId, int $albumPos, int $songPos, ArtistRepository $artistRepository, AlbumRepository $albumRepository, SongRepository $songRepository): Response
    {

        $artist = $artistRepository->find($artistId);

        if (!$artist) {
            throw $this->createNotFoundException('Artist not found');
        }

        $albums = $albumRepository->findBy(['artist' => $artist]);

        usort($albums, fn($a, $b) => $a->getId() <=> $b->getId());

        if ($albumPos < 1 || $albumPos > count($albums)) {
            throw $this->createNotFoundException('Song position out of range');
        }

        $album = $albums[$albumPos - 1];

        $songs = $songRepository->findBy(['album' => $album]);

        usort($songs, fn($a, $b) => $a->getId() <=> $b->getId());

        if ($songPos < 1 || $songPos > count($songs)) {
            throw $this->createNotFoundException('Song position out of range');
        }

        $song = $songs[$songPos - 1];

        return $this->render('songShow.html.twig', [
            'song' => $song,
        ]);
    }

}
