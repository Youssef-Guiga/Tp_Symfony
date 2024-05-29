<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
private $passwordHasher;
private $entityManager;

public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
{
$this->passwordHasher = $passwordHasher;
$this->entityManager = $entityManager;
}

public function register(User $user, string $plainPassword): void
{
// Encode the plain password
$user->setPassword(
$this->passwordHasher->hashPassword(
$user,
$plainPassword
)
);

// Persist the new user entity
$this->entityManager->persist($user);
$this->entityManager->flush();
}
}
