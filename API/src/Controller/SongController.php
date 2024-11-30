<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    #[Route('/songs', name: 'songs')]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findAll();

        return $this->render('songs.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/song/{id}', name: 'song_detail')]
    public function show(SongRepository $songRepository, int $id): Response
    {
        $song = $songRepository->find($id);

        if (!$song) {
            throw $this->createNotFoundException('Song not found');
        }

        return $this->render('songShow.html.twig', [
            'song' => $song,
        ]);
    }
}
