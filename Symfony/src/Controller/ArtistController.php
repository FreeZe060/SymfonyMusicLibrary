<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'artists')]
    public function index(ArtistRepository $artistRepository, Request $request): Response
    {
        $criteria = [
            'style' => $request->query->get('style', ''),
        ];

        $artists = $artistRepository->findAll();

        return $this->render('artists.html.twig', [
            'criteria' => $criteria,
            'artists' => $artists,
        ]);
    }

    #[Route('/artist/{artistId}', name: 'artist_detail')]
    public function show(ArtistRepository $artistRepository, int $artistId): Response
    {
        $artist = $artistRepository->find($artistId);

        if (!$artist) {
            throw $this->createNotFoundException('Artist not found');
        }

        return $this->render('artistShow.html.twig', [
            'artist' => $artist,
        ]);
    }
}
