<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/person', name: 'admin_person_')]
class PersonController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PersonRepository $personRepository): Response
    {
        return $this->render('person/index.html.twig', [
            'people' => $personRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success',sprintf("%s wurde erfolgreich aktualisiert.",$person->getName()));
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
            $entityManager->remove($person);
            $entityManager->flush();
            $this->addFlash('success',sprintf("%s wurde erfolgreich entfernt.",$person->getName()));
        }

        return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
    }
}
