<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\UploadPhotoType;
use App\Repository\PhotoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

                /** @var UploadedFile $pictureFileName */
                $pictureFileName = $form->get('filename')->getData();
                if($pictureFileName) {
                    $originalFileName = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFileName = $originalFileName .'_'. uniqid() .'.' . $pictureFileName->guessExtension();
                    $pictureFileName->move('images/hosting', $newFileName);

                    $entityPhotos = new Photo();
                    $entityPhotos->setFilename($newFileName);
                    $entityPhotos->setIsPublic($form->get('is_public')->getData());
                    $entityPhotos->setUploadedAt(new \DateTimeImmutable());
                    $entityPhotos->setUser($this->getUser());

                    $em->persist($entityPhotos);
                    $em->flush();

                    $this->addFlash('notice', 'Upload succeeded');
                }
            }
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView()
        ]);
    }
}
