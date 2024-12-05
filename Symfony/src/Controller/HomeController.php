<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use App\Repository\SongRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArtistRepository $artistRepository, AlbumRepository $albumRepository, SongRepository $songRepository): Response
    {
        $artistCount = $artistRepository->count([]);
        $albumCount = $albumRepository->count([]);
        $songCount = $songRepository->count([]);

        return $this->render('index.html.twig', [
            'artistCount' => $artistCount,
            'albumCount' => $albumCount,
            'songCount' => $songCount,
        ]);
    }
}
