<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\UploadPhotoType;
use App\Repository\PhotoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(UploadPhotoType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            if($this->getUser()) {
                $entityPhotos = new Photo();
                $entityPhotos->setFilename($form->get('filename')->getData());
                $entityPhotos->setIsPublic($form->get('is_public')->getData());
                $entityPhotos->setUploadedAt(new \DateTimeImmutable());
                $entityPhotos->setUser($this->getUser());

                $em->persist($entityPhotos);
                $em->flush();
            }
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView()
        ]);
    }
}
