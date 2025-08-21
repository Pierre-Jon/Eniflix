<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SeasonController extends AbstractController
{
    #[Route('/season/create', name: 'season_create')]
    #[isGranted('ROLE_MODERATEUR')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $season = new Season();

        $form = $this->createForm(SeasonType::class, $season);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($season);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle saison a été créé');
            return $this->redirectToRoute('serie_detail', ['id' => $season->getSerie()->getId()]);
            // A partie de saison il n'y a pas de getSerieId (d'ou les 2 méthode à la suite)
        }

        return $this->render('season/edit.html.twig', [
            'season_form' => $form,
        ]);
    }
}
