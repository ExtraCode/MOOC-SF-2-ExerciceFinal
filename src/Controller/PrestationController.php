<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\PrestationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prestation', name: 'app_prestation_')]
class PrestationController extends AbstractController
{
    #[Route('/', name: 'liste')]
    public function index(PrestationRepository $prestationRepository, UserRepository $userRepository): Response
    {

        $prestations = $prestationRepository->listPrestations(10);
        $lastUser = $userRepository->lastUserRegistered();

        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
            'lastUser' => $lastUser
        ]);
    }

    #[Route('/mesprestations', name: 'mesprestations')]
    public function mesPrestations(PrestationRepository $prestationRepository): Response
    {

        $prestations = $prestationRepository->findBy([
            'proprietaire' => $this->getUser()
        ]);

        return $this->render('prestation/mesprestations.html.twig', [
            'prestations' => $prestations
        ]);
    }

    #[Route('/ajouter', name: 'ajouter')]
    #[Route('/modifier/{id}', name: 'modifier')]
    public function ajouter(Request $request, PrestationRepository $prestationRepository, EntityManagerInterface $entityManager, int $id = null): Response
    {
        if ($request->attributes->get('_route') == "app_prestation_ajouter"){
            $prestation = new Prestation();
            $prestation->setProprietaire($this->getUser());
        }else{
            $prestation = $prestationRepository->find($id);

            // Controle l'accès à la prestation
            $controle = $this->controlerAccessUtilisateur($prestation);
            if($controle == "unauthorized"){

                return $this->redirectToRoute('app_prestation_mesprestations');

            }
        }

        $form = $this->createForm(PrestationType::class, $prestation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prestation->setDateCreation(new \DateTime());

            $entityManager->persist($prestation);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La prestation a bien été '.( $request->attributes->get('_route') == "app_prestation_ajouter" ? 'ajoutée' : 'modifiée' ). ' ! '
            );

            return $this->redirectToRoute('app_prestation_mesprestations');


        }

        return $this->renderForm('prestation/editer.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/supprimer/{id}', name: 'supprimer')]
    public function supprimer(PrestationRepository $prestationRepository, EntityManagerInterface $entityManager, int $id): Response
    {

        $prestation = $prestationRepository->find($id);

        // Controle l'accès à la prestation
        $controle = $this->controlerAccessUtilisateur($prestation);
        if($controle == "unauthorized"){

            return $this->redirectToRoute('app_prestation_mesprestations');

        }

        $entityManager->remove($prestation);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La prestation a bien été supprimée ! '
        );

        return $this->redirectToRoute('app_prestation_mesprestations');

    }

    // Assure qu'un utilisateur accède bien à l'un de ses prestations
    private function controlerAccessUtilisateur(Prestation $prestation){

        if($this->getUser() != $prestation->getProprietaire()){

            $this->addFlash(
                'danger',
                "Vous n'avez pas accès à cette prestation !"
            );

            return "unauthorized";

        }

        return "authorized";

    }
}
