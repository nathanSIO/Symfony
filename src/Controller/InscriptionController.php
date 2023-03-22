<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\Employe;
use App\Entity\Formation;
use App\Entity\Inscription;

class InscriptionController extends AbstractController
{
    #[Route('/inscriptionFormation/{id}', name: 'app_inscription_formation')]
    public function inscriptionFormation(ManagerRegistry $doctrine, $id): Response
    {
        $uneSession = new Session();
        $unEmploye = $doctrine->getManager()->getRepository(Employe::class)->find($uneSession->get("employeId"));
        $uneFormation = $doctrine->getManager()->getRepository(Formation::class)->find($id);

        $uneInscription = new Inscription();
        $uneInscription->setStatut("En attente");
        $uneInscription->setEmploye($unEmploye);
        $uneInscription->setFormation($uneFormation);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($uneInscription);
        $entityManager->flush();
        
        return $this->redirectToRoute("app_affiche_formation");
    }

    #[Route('/afficheGestionInscription', name: 'app_affiche_gestion_inscription')]
    public function afficheGestionInscription(ManagerRegistry $doctrine): Response
    {
        $lesInscriptions = $doctrine->getManager()->getRepository(Inscription::class)->findAll();
        
        if (!empty($lesInscriptions) || !$lesInscriptions == null) {
            return $this->render("inscription/afficheGestionInscription.html.twig", array("lesInscriptions" => $lesInscriptions));
        }
        else {
            return $this->render("inscription/afficheGestionInscription.html.twig");
        }
    }

    #[Route('/afficheGestionInscriptionEnAttente', name: 'app_affiche_gestion_inscription_enAttente')]
    public function afficheGestionInscriptionEnAttente(ManagerRegistry $doctrine): Response
    {
        $lesInscriptions = $doctrine->getManager()->getRepository(Inscription::class)->findAll();
        
        if (!empty($lesInscriptions)) {
            return $this->render("inscription/afficheGestionInscriptionEnAttente.html.twig", array("lesInscriptions" => $lesInscriptions));
        }
        else {
            return $this->render("inscription/afficheGestionInscriptionEnAttente.html.twig");
        }
    }

    #[Route('/modificationInscription/{id},{statut}', name: 'app_modification_inscription')]
    public function modificationInscription(ManagerRegistry $doctrine, $id, $statut): Response
    {
        $uneInscription = $doctrine->getManager()->getRepository(Inscription::class)->find($id);
        $uneInscription->setStatut($statut);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($uneInscription);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_affiche_gestion_inscription');  
    }

    #[Route('/afficheInscriptionEmploye/{id}', name: 'app_affiche_inscription_employe')]
    public function afficheInscriptionEmploye($id, ManagerRegistry $doctrine)
    {
        $lesInscriptions = $doctrine->getManager()->getRepository(Inscription::class)->chercherInscriptionEmploye($id);


        if (!empty($lesInscriptions)){
            return $this->render("inscription/afficheInscriptionEmploye.html.twig", array ('lesInscriptions' => $lesInscriptions));
        }
        else{
            return $this->render("inscription/afficheInscriptionEmploye.html.twig");
        }
    }
}
