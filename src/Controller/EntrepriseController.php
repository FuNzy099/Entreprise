<?php

namespace App\Controller;


use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprise", name="app_entreprise")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // On récupére les entreprises de la base de donnée
        $entreprises = $doctrine->getRepository(Entreprise::class)->findBy([], ["raisonSociale" => "ASC"]);
        // $entreprises ci dessus est une requete DQL ! 
        // Elle serait traduite en SQL par => SELECT * FROM entreprise ORDER BY raisonSociale ASC

        // Une fois les entreprises récupéré on les envoies dans la vue
        return $this->render('entreprise/index.html.twig', [

            "entreprises" => $entreprises
        
        ]);
    }



    // todo ------- Public function pour ajouter une entreprise

    /**
     * @Route("/entreprise/add", name="add_entreprise")
     * @Route("/entreprise/{idEntreprise}/edit", name="edit_entreprise")
     * @ParamConverter("entreprise", options={"mapping": {"idEntreprise": "id"}})
     */
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): response
    {

        if(!$entreprise) {
            $entreprise = new Entreprise();
        }

        // Permet de construire un formulaire en ce reposant sur le builder de EntrepriseType
        $form = $this -> createForm(EntrepriseType::class, $entreprise);
        $form -> handleRequest($request);

        // isSubmitted => le formulaire est-il envoyé (submit)
        // isValid => Permet de verifier si les données remplient dans le formulaire sont intègre 
        if($form -> isSubmitted() && $form -> isValid()){

            //  $form -> getData() : On récupére les données saisi dans le formulaire pour les injecter(hydrater) dans l'objet entreprise
            $entreprise = $form -> getData();
            $entityManager =  $doctrine -> getManager();
            // prepare
            $entityManager -> persist($entreprise);
            // insert into (execute)
            $entityManager -> flush();

            return $this -> redirectToRoute('app_entreprise');
        }

        // Permet d'afficher le formulaire d'ajout d'une entreprise dans la vue
        return $this->render('entreprise/add.html.twig', [

           'formAddEntreprise' => $form -> createView(),
           
            // on verifie si l'entreprise hesiste, cela nous permettra d'utiliser edit dans add.html.twig de entreprise dans le but de conditionner le h1
           'edit' => $entreprise -> getId() 
        
        ]);

    }



    // todo ------- Public function pour supprimer une entreprise

    /**
     * @Route("/entreprise/{idEntreprise}/delete", name="delete_entreprise")
     * @ParamConverter("entreprise", options={"mapping": {"idEntreprise": "id"}})
     */
    public function delete(ManagerRegistry $doctrine, entreprise $entreprise): response 
    {

        $entityManager = $doctrine -> getManager();
        $entityManager -> remove($entreprise);
        $entityManager -> flush();

        return $this -> redirectToRoute('app_entreprise');

    }



    // todo ------- Public function qui permet d'afficher le detail des informations d'une entreprise

    /**
     * @Route("/entreprise/{idEntreprise}", name="show_entreprise")
     * @ParamConverter("entreprise", options={"mapping": {"idEntreprise": "id"}})
     */
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [

            "entreprise" => $entreprise
        
        ]);
    }
}


