<?php

namespace App\Controller;

use App\Entity\Election;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/election', name: 'election_')]
class ElectionController extends AbstractController
{

    #[Route('/election/{id}', name: 'show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $election = $entityManager->getRepository(Election::class)->find($id);

        return $this->render('election/index.html.twig', [
            'election' => $election
        ]);
    }
}
