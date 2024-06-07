<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\ElectionCode;
use App\Entity\ElectionResult;
use App\Entity\Person;
use App\Repository\ElectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/', name: 'election_')]
class ElectionSelectController extends AbstractController
{

    #[Route('/{uuid}', name: 'show')]
    public function show(Request $request, EntityManagerInterface $entityManager, Uuid $uuid): Response
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
        ];

        $election = $entityManager->getRepository(Election::class)->findOneBy(['uuid' => $uuid]);

        $submittedToken = $request->request->get('token');

        if($request->isMethod('post') and $this->isCsrfTokenValid('elect', $submittedToken)) {

            $code = $request->request->get('code');

            $electionCode = $entityManager->getRepository(ElectionCode::class)->findOneBy(
                ['code' => $code, 'election' => $election,'used' => null]
            );

            if($electionCode) {

                $electionCode->setUsed(true);
                $entityManager->persist($electionCode);
                $entityManager->flush();

                if($request->request->get('invalidate')=='invalidate') {
                    $this->addFlash('success','Die Wahl wurde ungültig gemacht!');
                } else {
                    foreach ($request->request->all('election') as $key => $value) {
                        $electionResult = new ElectionResult();
                        $electionResult->addElection($election);
                        $person = $entityManager->getRepository(Person::class)->find($value);
                        $electionResult->addPerson($person);
                        $entityManager->persist($electionResult);
                        $entityManager->flush();
                    }
                    $this->addFlash('success','Die Wahl war erfolgreich!');
                }
                return $this->redirectToRoute('app_index');

            } else {
                $this->addFlash('warning','Der Wahlcode ist ungültig!');
            }

        }

        return $this->render('election-select/index.html.twig', [
            'election' => $election,
            'colors' => $backgroundColor
        ]);
    }

    #[Route('/results', name: 'results')]
    public function index(ElectionRepository $electionRepository, EntityManagerInterface $entityManager,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if(!$authorizationChecker->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_index');
        }

        $elections = $electionRepository->findAllJoinResults();

        return $this->render('election-select/results.html.twig', [
            'elections' => $elections,
        ]);
    }

}
