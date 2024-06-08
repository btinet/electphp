<?php

namespace App\Controller;

use App\Entity\Election;
use Doctrine\ORM\EntityManagerInterface;
use Pontedilana\PhpWeasyPrint\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
class AppController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $elections = $entityManager->getRepository(Election::class)->findBy(['isActive' => true]);

        return $this->render('app/index.html.twig', [
            'elections' => $elections,
        ]);
    }

    #[Route('/export/{id}/html', name: 'export_qr_html', methods: ['GET'])]
    public function viewElectionCodes(Election $election, Pdf $pdf): Response
    {
        return $this->render('election/export_codes.html.twig',[
            'election' => $election
        ]);
    }

    #[Route('/privacy', name: 'privacy')]
    public function showPrivacy(): Response
    {
        return $this->render('app/privacy.html.twig');
    }

    #[Route('/imprint', name: 'imprint')]
    public function showImprint(): Response
    {
        return $this->render('app/imprint.html.twig');
    }

}
