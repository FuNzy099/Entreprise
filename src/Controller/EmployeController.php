<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="app_employe")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // On récupére les entreprises de la base de donnée
        $employes = $doctrine->getRepository(Employe::class)->findBy([], ["nom" => "ASC"]);
        // $employes ci dessus est une requete DQL ! 
        // Elle serait traduite en SQL par => SELECT * FROM employe ORDER BY nom ASC

        // Une fois les entreprises récupéré on les envoies dans la vue
        return $this->render('employe/index.html.twig', [

            "employes" => $employes
        
        ]);
    }



     // todo ------- Public function pour ajouter une employe

    /**
     * @Route("/employe/add", name="add_employe")
     * @Route("/employe/{idEmploye}/edit", name="edit_employe")
     * @ParamConverter("employe", options={"mapping": {"idEmploye": "id"}})
     */ 
    public function add(ManagerRegistry $doctrine, Employe $employe = null, Request $request): response
    {

        if(!$employe) {
            $employe = new Employe();
        }

        // Permet de construire un formulaire en ce reposant sur le builder de EntrepriseType
        $form = $this -> createForm(EmployeType::class, $employe);
        $form -> handleRequest($request);

        // isSubmitted => le formulaire est-il envoyé (submit)
        // isValid => Permet de verifier si les données remplient dans le formulaire sont intègre 
        if($form -> isSubmitted() && $form -> isValid()){

            //  $form -> getData() : On récupére les données saisi dans le formulaire pour les injecter(hydrater) dans l'objet employe
            $employe = $form -> getData();
            $entityManager =  $doctrine -> getManager();
            // prepare
            $entityManager -> persist($employe);
            // insert into (execute)
            $entityManager -> flush();

            return $this -> redirectToRoute('app_entreprise');
        }

        // Permet d'afficher le formulaire d'ajout d'une employe dans la vue
        return $this->render('employe/add.html.twig', [

           'formAddEmploye' => $form -> createView(),

            // on verifie si l'employe hesiste, cela nous permettra d'utiliser edit dans add.html.twig de employe dans le but de conditionner le h1
            'edit' => $employe -> getId() 
        
        ]);

    }



    // todo ------- Public function pour supprimer un employé

    /**
     * @Route("/employe/{idEmploye}/delete", name="delete_employe")
     * @ParamConverter("employe", options={"mapping": {"idEmploye": "id"}})
     */
    public function delete(ManagerRegistry $doctrine, Employe $employe): response 
    {

        $entityManager = $doctrine -> getManager();
        $entityManager -> remove($employe);
        $entityManager -> flush();

        return $this -> redirectToRoute('app_employe');

    }



    // todo ------- Public function qui permet d'afficher le detail des informations d'un employé

    /**
     * @Route("/employe/{idEmploye}", name="show_employe")
     * @ParamConverter("employe", options={"mapping": {"idEmploye": "id"}})
     */
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [

            "employe" => $employe
        
        ]);
    }


}
