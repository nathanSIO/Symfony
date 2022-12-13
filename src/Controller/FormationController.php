<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;

class FormationController extends AbstractController
{
    #[Route ('afficheFormation', name: 'app_affiche_formation')]
    public function afficherFormations(ManagerRegistry $doctrine): Response 
    {
        $lesFormations = $doctrine->getManager()->getRepository(Formation::class)->findAll();
        
        return $this->render("formation/afficheFormation.html.twig", array("lesFormations" => $lesFormations));
    }

    #[Route ('afficheGestionFormation', name: 'app_affiche_gestion_formation')]
    public function afficherGestionFormations(ManagerRegistry $doctrine, $lesFormations=null): Response 
    {
        $lesFormations = $doctrine->getManager()->getRepository(Formation::class)->findAll();
        
        return $this->render("formation/afficheGestionFormation.html.twig", array("lesFormations" => $lesFormations));
    }


    #[Route('/ajoutFormation', name: 'app_ajout_formation')]
    public function ajoutFormation(Request $request, ManagerRegistry $doctrine, $uneFormation = null): Response
    {
        if ($uneFormation == null) {
            $uneFormation = new Formation();
        }
        $formulaire = $this->createForm(FormationType::class, $uneFormation);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($uneFormation);
            $entityManager->flush();
            return $this->redirectToRoute('app_affiche_gestion_formation');
        }
        return $this->render('formation/ajoutFormation.html.twig', array('formulaire' => $formulaire->createView()));
    }


    #[Route('modificationFormation/{id}', name:'app_modification_formation')]
    public function modificationFormation(Request $request,ManagerRegistry $doctrine, $id= null)
    {
        $uneFormation = $doctrine->getManager()->getRepository(Formation::class)->find($id);

        $form = $this->createForm(FormationType::class, $uneFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine ->getManager();
            $entityManager ->persist($uneFormation);
            $entityManager -> flush();
            return $this->redirectToRoute('app_affiche_gestion_formation');
        }
        return $this->render('formation/modificationFormation.html.twig', array('formulaire'=>$form->createView()));        
    }

    #[Route('/suppressionFormation/{id}', name: 'app_suppression_formation')]
    public function suppressionFormation(ManagerRegistry $doctrine, $id): Response
    {
        $uneFormation = $doctrine->getManager()->getRepository(Formation::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($uneFormation);
        $entityManager->flush();
        return $this->redirectToRoute('app_affiche_formation');
    }
}