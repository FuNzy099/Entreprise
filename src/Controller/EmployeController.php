<?php

namespace App\Controller;

use App\Entity\Employe;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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


    // todo ------- Public function qui permet d'afficher le detail des informations d'un employé

    /**
     * @Route("/employe/{id}", name="show_employe")
     */
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [

            "employe" => $employe
        
        ]);
    }


}
