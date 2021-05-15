<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $newUser = new User();
        $form = $this->createForm(RegistrationType::class, $newUser);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($newUser, $newUser->getPassword());
            $newUser->setPassword($password);
            $this->em->persist($newUser);
            $this->em->flush();
            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('registration');
        }
        return $this->render("registration/index.html.twig", [
            'form' => $form->createView(),
        ]);

        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }
}
