<?php
namespace App\Controller;

use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
private $loginService;

public function __construct(LoginService $loginService)
{
$this->loginService = $loginService;
}

#[Route(path: '/login', name: 'app_login')]
public function login(): Response
{
$loginData = $this->loginService->getLoginData();

return $this->render('security/login.html.twig', $loginData);
}

#[Route(path: '/logout', name: 'app_logout')]
public function logout(): void
{
throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
}
}
