<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie')]
final class SerieController extends AbstractController
{

    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    public function list(SerieRepository $serieRepository, int $page, ParameterBagInterface $parameters): Response
    {
        //$series = $serieRepository->findAll();

        $nbPerPage = $parameters->get('serie')['nb_max'];
        $offset = ($page - 1) * $nbPerPage;
        $criterias = [
//            'status' => 'Returning',
//            'genre' => 'Drama',
        ];

        $series = $serieRepository->findBy(
            $criterias,
            ['popularity' => 'DESC'],
            $nbPerPage,
            $offset
        );

        $total = $serieRepository->count($criterias);
        $totalPages = ceil($total / $nbPerPage);

        return $this->render('serie/list.html.twig', [
                'series' => $series,
                'page' => $page,
                'total_pages' => $totalPages,
            ]
        );
    }

    #[Route('/liste-custom', name: '_custom_list')]
    public function listCustom(SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findSeriesCustom(400, 8);
        $series = $serieRepository->findSeriesWithDQL(400, 8);

        // Le requêtage SQL raw nécessite qu'on adapte le template (firstAirDate -> first_air_date)
        //$series = $serieRepository->findSeriesWithSQL(400, 8);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => 1,
            'total_pages' => 10,
        ]);
    }



    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
        // Ici symfony comprend que le clé(id) correspond à l'objet Serie
        // Plus besoin du SerieRepository
    {

        if (!$serie) {
            throw $this->createNotFoundException('Pas de série pour cet id');
        }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    // Formualire - création d'une serie
    #[Route('/create', name: '_create', requirements: ['id' => '\d+'])]
    public function create(Request $request, EntityManagerInterface $em) : Response{

        $serie = new Serie();
        //Création du formulaire
        $form = $this->createForm(SerieType::class, $serie);

        // Gère la requeete
        $form->handleRequest($request);

        // Est ce que ce form est soumis ?
        if ($form->isSubmitted()) {
//            $serie->setDateCreated(new \DateTime());
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une serie a bien été ajouté');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig',[
            'serie_form' => $form
        ]);
    }

    // Formualire - mise à jour d'une serie
    #[Route('/update{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Serie $serie, Request $request, EntityManagerInterface $em) : Response{

        //Création du formulaire
        $form = $this->createForm(SerieType::class, $serie);

        // Gère la requeete
        $form->handleRequest($request);

        // Est ce que ce form est soumis ?
        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash('success', 'Une serie a été mis à jour');

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig',[
            'serie_form' => $form
        ]);
    }

}
