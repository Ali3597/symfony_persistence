<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Search;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\SearchType;



class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    public function index(Request $request,LivreRepository $livreRepository): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
      
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findLivresDependsOnSearch($search->getQueryName()),
            "form" => $form->createView(),
        ]);
    }

    #[Route('/withDeleted', name: 'app_livre_deleted', methods: ['GET'])]
    public function withDeleted(Request $request,LivreRepository $livreRepository): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findLivresDependsOnSearchDeleted($search->getQueryName()),
            "form" => $form->createView(),
        ]);
    }
   
   
 

    #[Route('/livre/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivreRepository $livreRepository): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setDeleted(FALSE);
            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livre/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $livre->setDeleted(True);
            $livreRepository->save($livre, true);
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
