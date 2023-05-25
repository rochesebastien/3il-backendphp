<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;

use Psr\Log\LoggerInterface;

#[Route('/serie')]
class SerieController extends AbstractController
{
    #[Route('/', name: 'app_serie_index', methods: ['GET'])]
    public function index(SerieRepository $serieRepository): Response
    {
        // $series = $serieRepository->findBy(['note' => 3]);
        $series = $serieRepository->findAll();
        return $this->render('serie/index.html.twig', [
            'series' => $series,
        ]);
    }

    #[Route('/new', name: 'app_serie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SerieRepository $serieRepository): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serieRepository->save($serie, true);

            return $this->redirectToRoute('app_serie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('serie/new.html.twig', [
            'serie' => $serie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_serie_show', methods: ['GET'])]
    public function show(Serie $serie): Response
    {
        return $this->render('serie/show.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/{id}/note/{note}', name: 'app_serie_edit', methods: ['GET'])]
    public function edit(Serie $serie, SerieRepository $serieRepository,Request $request,LoggerInterface $logger): Response
    {
        $id = $request->get('id');
        $note = $request->get('note');
        $serie = $serieRepository->find($id);

        if ($serie) {
            $serieRepository->changeNote($serie,$note,true); 
            $serieRepository->save($serie, true);   
            $logger->info('La note de la série a été modifiée.', ['serie_id' => $id, 'new_note' => $note]);
            return new Response("Note modifié avec succes : $note");
        } else {
            throw new \InvalidArgumentException('Série non trouvée');
        }
    }

    #[Route('/{id}', name: 'app_serie_delete', methods: ['POST'])]
    public function delete(Request $request, Serie $serie, SerieRepository $serieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->request->get('_token'))) {
            $serieRepository->remove($serie, true);
        }

        return $this->redirectToRoute('app_serie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('{id}/episode', name: 'app_serie_newep', methods: ['GET'])]
    public function newEpisode(Request $request, EpisodeRepository $episodeRepository): Response
    {
        $episode = new Episode();
        $id = $request->get('id');
        $episode->setSerie($id);
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeRepository->save($episode, true);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }
}
