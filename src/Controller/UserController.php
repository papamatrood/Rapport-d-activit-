<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfileFormType;
use App\Form\EditPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/index.html.twig', [
            'registrationForm' => $form->createView(),
            'users' => $userRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->save($user, true);

    //         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    // public function show(User $user): Response
    // {
    //     return $this->render('user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/password-edit', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    public function passwordEdit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($passwordHasher->isPasswordValid($user, $form->get('lastPassword')->getData())) {
                $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                );
                $user->setPassword($encodedPassword);

                $this->entityManager->flush();

                $u = $this->getUser();

                if ($user->getId() === $u->getId()) {
                    return $this->redirectToRoute('app_logout');
                }

                return $this->redirectToRoute('app_rapport_index');
            }
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }
}
