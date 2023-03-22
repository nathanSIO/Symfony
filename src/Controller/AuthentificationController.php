<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Employe;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\ConnexionType;


class AuthentificationController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(Request $request, ManagerRegistry $doctrine, $unEmploye = null): Response
    {
        if ($unEmploye == null) {
            $unEmploye = new Employe();
        }

        $formulaire = $this->createForm(ConnexionType::class, $unEmploye);
        $formulaire->handleRequest($request);

        try {
            if ($formulaire->isSubmitted() && $formulaire->isValid()) {
                $unEmploye = $doctrine->getManager()->getRepository(Employe::class)->verificationConnexion($unEmploye->getLogin(), $unEmploye->getPassword());
                if ($unEmploye) {
                    $uneSession = new Session();
                    $uneSession->set("employeId", $unEmploye->getId());
                    return $this->redirectToRoute('app_espace_employe');
                }
                else 
                return $this->redirectToRoute('app_authentification');
            }
        } catch (\Throwable $th) {
            $message = "Identifiant ou mot de passe inccorect";
            return $this->render('authentification/connexion.html.twig', array('formulaire' => $formulaire->createView(), "message" => $message));
        }

        return $this->render('authentification/connexion.html.twig', array('formulaire' => $formulaire->createView()));
    }

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function deconnexion(): Response
    {
        $uneSession = new Session();
        $uneSession->clear();
        return $this->redirectToRoute("app_connexion");
    }
} 