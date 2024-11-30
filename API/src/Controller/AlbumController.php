<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/albums', name: 'albums')]
    public function index(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();

        return $this->render('albums.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/album/{id}', name: 'album_detail')]
    public function show(AlbumRepository $albumRepository, int $id): Response
    {
        $album = $albumRepository->find($id);

        if (!$album) {
            throw $this->createNotFoundException('Album not found');
        }

        return $this->render('albumShow.html.twig', [
            'album' => $album,
        ]);
    }
}
