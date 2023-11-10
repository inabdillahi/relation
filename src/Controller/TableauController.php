<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Form\SearchType;
use App\Services\Search;
use App\Form\RegisterType;
use App\Entity\Utilisateurs;
use App\Services\Identifiant;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateursRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TableauController extends AbstractController
{
    private $identifiant;
    private $repoUser;
    private $manager;
    public function __construct(Identifiant $identifiant, UtilisateursRepository $repoUser, EntityManagerInterface $manager)
    {
        $this->identifiant = $identifiant;
        $this->repoUser = $repoUser;
        $this->manager = $manager;
    }

    #[Route('/tableau', name: 'tableau')]
    public function index(): Response
    {
        return $this->render('tableau/tableau.html.twig', [
            "titre" => 'tableau'
        ]);
    }


    #[Route('/tableau/admin/gestions-client', name: 'g-client')]
    public function g_Client(Request $request, UtilisateursRepository $repo, PaginatorInterface $pagination): Response
    {


        // Rechercher 
        $search = new Search();
        $fo = $this->createForm(SearchType::class, $search);
        $fo->handleRequest($request);


        //Pagination
        $users = $repo->findByFonction("CLIENT");
        $users = $pagination->paginate(
            $repo->findAllWithPagination($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('tableau/g-client.html.twig', [
            "titre" => 'client',
            "users" => $users,
            "form" => $fo->createView()
        ]);
    }

    #[Route('/tableau/admin/gestions-client/enregistrer-client', name: 'ajouter-client')]
    public function ajouter_Client(Request $request, UserPasswordHasherInterface $hash): Response
    {


        $user = new Utilisateurs;


        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailExiste = $this->repoUser->findOneByEmail($user->getEmail());
            if (!$emailExiste) {
                $user->setFonction("CLIENT");
                $passwordHash = $hash->hashPassword($user, $user->getPassword());
                $user->setPassword($passwordHash);
                $user->setIdentifiant($this->identifiant->codeIdentifiant(7));
                $user->setCreatedAt(new DateTimeImmutable());
                $user->setSexe($request->request->get('sexe'));
                $user->setFonction($request->request->get('fonction'));
                $user->setStatus(0);
                $user->setToken($this->identifiant->codeIdentifiant(20));
                $user->setVerify(0);
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash('success', 'Vous avez bien inscrit, veuillez verifier votre e-mail');
                return $this->redirectToRoute('g-client');
            } else {
                $this->addFlash('success', 'Cette email existe deja');
                return $this->redirectToRoute('ajouter-client');
            }
        }
        return $this->render('tableau/ajouterClient.html.twig', [
            "titre" => 'client',
            'form' => $form->createView()
        ]);
    }



    //Supprimer un User

    #[Route('/tableau/admin/gestions-client/supprimer/{id}', name: 'supprimer-client')]
    public function SupprimerUser(Utilisateurs $user, Request $req)
    {
        if ($this->isCsrfTokenValid("SUP" . $user->getId(), $req->get('_token'))) {
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash("success", "La suppression à bien été effectuer");
            return $this->redirectToRoute('g-client');
        }
    }


    // Voir un client 


    #[Route('/tableau/admin/gestions-client/client/{id}', name: 'voir-client')]
    public function VoirClient($id): Response
    {
        $user = $this->repoUser->find($id);
        return $this->render('tableau/voirClient.html.twig', [
            "titre" => 'client',
            "client" => $user
        ]);
    }
}
