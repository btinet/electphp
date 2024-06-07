<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\ElectionCode;
use App\Entity\Person;
use App\Form\ElectionType;
use App\Repository\ElectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use ParseCsv\Csv;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/election', name: 'crud_election_')]
class ElectionController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ElectionRepository $electionRepository): Response
    {
        return $this->render('election/index.html.twig', [
            'elections' => $electionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $election = new Election();
        $form = $this->createForm(ElectionType::class, $election);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $codeAmount = intval($form->get('code-amount')->getData());
            $entityManager->persist($election);
            if($codeAmount <= 0 || !is_int($codeAmount)) {
                $codeAmount = 100;
            }
            for ($i = 1; $i <= $codeAmount; $i++) {
                $code = new ElectionCode();
                $code->setCode($this->generateCode(5));
                $code->setElection($election);
                $entityManager->persist($code);
            }
            $entityManager->flush();
            $this->addFlash('success',sprintf("%s wurde erfolgreich mit %s Wahlcodes angelegt.",$election->getLabel(),$codeAmount));

            return $this->redirectToRoute('crud_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/new.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Election $election): Response
    {
        $backgroundColor = [
            '#fff100',
            '#ff8c00',
            '#e81123',
            '#ec008c',
            '#68217a',
            '#00188f',
            '#00bcf2',
            '#00b294',
            '#009e49',
            '#bad80a',
            '#fff100',
            '#ff8c00',
            '#e81123',
            '#ec008c',
            '#68217a',
            '#00188f',
            '#00bcf2',
            '#00b294',
            '#009e49',
            '#bad80a',
        ];

        return $this->render('election/show.html.twig', [
            'election' => $election,
            'colors' => $backgroundColor
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {

        foreach ($election->getCodes() as $code) {
            /** @var $code ElectionCode */
            if($code->isUsed()) {
                $this->addFlash('warning',sprintf("Wahlrunde '%s' hat bereits begonnen. Änderungen nicht mehr möglich.",$election));
                return $this->redirectToRoute('crud_election_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(ElectionType::class, $election);
        $form->get('code-amount')->setData($election->getCodes()->count());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $codeAmount = intval($form->get('code-amount')->getData());
            if($codeAmount > 0 && $codeAmount != $election->getCodes()->count()) {
                foreach ($election->getCodes() as $code) {
                    $election->removeCode($code);
                }
                for ($i = 1; $i <= $codeAmount; $i++) {
                    $code = new ElectionCode();
                    $code->setCode($this->generateCode(5));
                    $code->setElection($election);
                    $entityManager->persist($code);
                }
                $this->addFlash('success',sprintf("%s wurde erfolgreich mit %s Wahlcodes aktualisiert.",$election->getLabel(),$codeAmount));
            } else {
                $this->addFlash('success',sprintf("%s wurde erfolgreich aktualisiert.",$election->getLabel()));
            }

            $entityManager->flush();

            return $this->redirectToRoute('crud_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/edit.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$election->getId(), $request->request->get('_token'))) {

            foreach ($election->getPeople() as $person) {
                $election->removePerson($person);
            }
            foreach ($election->getCodes() as $code) {
                $election->removeCode($code);
            }
            foreach ($election->getElectionResults() as $result) {
                $election->removeElectionResult($result);
            }
            $entityManager->remove($election);
            $entityManager->flush();
            $this->addFlash('success',sprintf("%s wurde erfolgreich entfernt.",$election->getLabel()));
        }

        return $this->redirectToRoute('crud_election_index', [], Response::HTTP_SEE_OTHER);
    }

    private function generateCode($length): string
    {
        //Mögliche Zeichen für den String
        // $chars = '0123456789';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        //String wird generiert
        $str = '';
        $anz = strlen($chars);
        for ($i=0; $i<$length; $i++) {
            $str .= $chars[rand(0,$anz-1)];
        }
        return $str;
    }

    #[Route('/export/{id}', name: 'export_codes', methods: ['GET'])]
    public function exportCodes(Election $election): StreamedResponse
    {
        $data_array = [];
        $i = 0;

        foreach ($election->getCodes() as $code){
            /** @var $code ElectionCode */
            if(!$code->isUsed()) {
                $data_array[$i] = [
                    $election->getLabel(),
                    $code->getCode()
                ];
                $i++;
            }
        }

        $csv = new Csv();
        $csv->linefeed = "\n";

        $header = array('Wahlrunde', 'Code');
        $date = date('ymd');
        $csv->output($date.'-wahlcodes.csv', $data_array, $header, ';');
        $response = new StreamedResponse(function() use ($csv) {
        });
        $response->headers->set('Content-Type', 'text/csv');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $csv->output_filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    #[Route('/{id}/switch', name: 'switch', methods: ['POST'])]
    public function addLike(Request $request, Election $election, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = json_decode($request->getContent(),true);

        $election->setIsActive(!$election->isIsActive());
        $entityManager->persist($election);
        $entityManager->flush();


        return new JsonResponse([
            'electionActive' => $election->isIsActive(),
        ]);
    }

}
