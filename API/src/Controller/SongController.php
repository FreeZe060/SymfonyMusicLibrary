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
    public function index(SongRepository $songRepository, AlbumRepository $albumRepository, Request $request): Response
    {
        $criteria = [
            'title' => $request->query->get('query', ''),
            'genre' => $request->query->get('genre', ''),
            'min_duration' => $request->query->get('min_duration', 0),
            'max_duration' => $request->query->get('max_duration', 600),
        ];

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
    #[Route('/api/artist/{artistId}/album/{albumPos}/song/{songPos}', name: 'song_detailAPI', methods: ['GET'])]
    public function songDetailAPI(int $artistId, int $albumPos, int $songPos, ArtistRepository $artistRepository, AlbumRepository $albumRepository, SongRepository $songRepository): Response
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

        return $this->json([
            'song' => $song,
        ]);
    }

    /**
     * Cette route recherche des chansons par une plage de durée.
     * 
     * @Route("/api/songs/search-by-duration", name="song_search_by_duration", methods={"GET"})
     * @Operation(
     *     summary="Recherche des chansons par durée",
     *     description="Cette API permet de rechercher toutes les chansons dont la durée est dans un intervalle spécifié.",
     *     tags={"Songs"},
     *     responses={
     *         "200": {
     *             "description": "Liste des chansons",
     *             "content": {
     *                 "application/json": {
     *                     "schema": {
     *                         "type": "array",
     *                         "items": { "$ref": "#/components/schemas/Song" }
     *                     }
     *                 }
     *             }
     *         },
     *         "400": {
     *             "description": "Paramètres manquants ou invalides"
     *         }
     *     }
     * )
     */

    #[Route('/api/songs/search-by-duration', name: 'song_search_by_duration', methods: ['GET'])]
    public function searchByDuration(SongRepository $songRepository, Request $request): Response
    {
        $minDuration = $request->query->get('min_duration');
        $maxDuration = $request->query->get('max_duration');

        if (null === $minDuration || null === $maxDuration) {
            return $this->json([
                'error' => 'Both min_duration and max_duration must be provided.'
            ], 400);
        }

        $minDuration = (int)$minDuration;
        $maxDuration = (int)$maxDuration;

        $songs = $songRepository->findByDurationInterval($minDuration, $maxDuration);

        return $this->json([
            'songs' => $songs
        ]);
    }
}
