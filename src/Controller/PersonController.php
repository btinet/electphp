<?php

namespace App\Controller;

use App\Entity\ElectionResult;
use App\Entity\Person;
use App\Entity\User;
use App\Form\PersonEditType;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/person', name: 'admin_person_')]
class PersonController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PersonRepository $personRepository, UserRepository $userRepository): Response
    {
        $people = [];
        if($this->isGranted('ROLE_SUPER_ADMIN')) {
            $people = $personRepository->findAll();
        } elseif($this->isGranted('ROLE_ADMIN')) {
            $user = $userRepository->find($this->getUser());
            $people = $personRepository->findBy(['user' => $user]);
        }
        return $this->render('person/index.html.twig', [
            'people' => $people,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person->setUser($entityManager->find(User::class,$this->getUser()));
            $entityManager->persist($person);
            $entityManager->flush();
            $this->addFlash('success',sprintf("%s wurde erfolgreich angelegt.",$person->getName()));
            return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('person/new.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Person $person): Response
    {
        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonEditType::class, $person);
        $electionData = $form->get('election')->getData();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                if($electionData != $person->getElection()) {
                    foreach ($person->getElectionResults() as $result) {
                        $entityManager->remove($result);
                    }

                    $this->addFlash('warning',sprintf("Die Wahl hat sich geändert. Wahlergebnisse werden für %s entfernt.",$person));
                }
                $entityManager->flush();
                $this->addFlash('success',sprintf("%s wurde erfolgreich aktualisiert.", $person->getName()));

            return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('person/edit.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            if($this->isGranted('ROLE_SUPER_ADMIN') || $person->getUser() === $this->getUser()) {
                foreach ($person->getElectionResults() as $result) {
                    $entityManager->remove($result);
                }
                $entityManager->remove($person);
                $entityManager->flush();
                $this->addFlash('success', sprintf("%s wurde erfolgreich entfernt.", $person->getName()));
            } else {
                $this->addFlash('warning',"Ihnen fehlt die notwendige Berechtigung");
            }
        }

        return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
    }
}
