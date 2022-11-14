<?php

namespace App\Controller;

use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LatestPhotosController extends AbstractController
{
    #[Route('/latest', name: 'app_latest_photos')]
    public function index(ManagerRegistry $doctrine) {

        $em = $doctrine->getManager();
        $latestPhotosPublic = $em->getRepository(Photo::class)->findBy(['is_public' => true]);

        return $this->render('latest_photos/index.html.twig', [
            'latestPhotosPublic' => $latestPhotosPublic
        ]);
    }
}