<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Employe;
use Symfony\Component\HttpFoundation\Session\Session;

class EmployeController extends AbstractController
{
    #[Route('/espaceEmploye', name: 'app_espace_employe')]
    public function espaceEmploye(ManagerRegistry $doctrine): Response
    {
        try {
            $uneSession = new Session();
            $unEmploye = $doctrine->getManager()->getRepository(Employe::class)->find($uneSession->get("employeId"));

            if ($unEmploye->getStatut() == 0) {
                return $this->render("employe/espaceEmploye.html.twig", array("unEmploye" => $unEmploye));
            }
            elseif ($unEmploye->getStatut() == 1) {
                return $this->render("employe/espaceGestionEmploye.html.twig", array("unEmploye" => $unEmploye));
            }
        } catch (\Throwable $th) {
            return $this->redirectToRoute("app_connexion");
        }
    }

    #[Route('/afficheEmploye', name: 'app_affiche_employe')]
    public function afficheFormation(ManagerRegistry $doctrine): Response
    {
        $lesEmployes = $doctrine->getManager()->getRepository(Employe::class)->findAll();
        
        return $this->render("employe/afficheEmploye.html.twig", array("lesEmployes" => $lesEmployes));
    }

    #[Route('/suppressionEmploye/{id}', name: 'app_suppression_employe')]
    public function suppressionFormation(ManagerRegistry $doctrine, $id): Response
    {
        $unEmploye = $doctrine->getManager()->getRepository(Employe::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($unEmploye);
        $entityManager->flush();
        return $this->redirectToRoute('app_affiche_employe');
    }
    #[Route('rechercheEmploye', name: "app_recherche_employe")]
    public function rechercheEmploye(ManagerRegistry $doctrine): Response
    {
        $lesEmployesRech = $doctrine->getManager()->getRepository(Employe::class)->find("nomEmploye");
        
        return $this->render("employe/rechercheEmploye.html.twig", array("lesEmployesRech" => $lesEmployesRech));
    }
}
