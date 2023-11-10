<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Form\RegisterType;
use App\Entity\Utilisateurs;
use App\Services\Identifiant;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompteController extends AbstractController
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
    #[Route('/inscription', name: 'inscription')]
    public function index(Request $request, UserPasswordHasherInterface $hash): Response
    {
        $user = new Utilisateurs;

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailExiste = $this->repoUser->findOneByEmail($user->getEmail());
            if (!$emailExiste) {
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
                return $this->redirectToRoute('inscription');
            } else {
                $this->addFlash('success', 'Cette email existe deja');
                return $this->redirectToRoute('inscription');
            }
        }
        return $this->render('compte/inscrire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
