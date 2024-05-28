<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
#[Route('/register', name: 'app_register')]
public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
$user = new User();
$form = $this->createForm(RegistrationFormType::class, $user);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
// Encode the plain password
$user->setPassword(
$passwordHasher->hashPassword(
$user,
$form->get('password')->getData()
)
);

// Set other properties
$user->setEmail($form->get('email')->getData());
$user->setPhonenumber($form->get('phonenumber')->getData());
    $user->setRoles(['ROLE_USER']);

// Persist the new user entity
$entityManager->persist($user);
$entityManager->flush();

// You can add a flash message or redirect as needed
$this->addFlash('success', 'Registration successful!');

return $this->redirectToRoute('app_login');
}

return $this->render('registration/register.html.twig', [
'registrationForm' => $form->createView(),
]);
}
}
