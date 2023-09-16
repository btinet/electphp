<?php

namespace App\Controller;

use App\Entity\Election;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
class AppController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $elections = $entityManager->getRepository(Election::class)->findAll();

        return $this->render('app/index.html.twig', [
            'elections' => $elections,
        ]);
    }
}
