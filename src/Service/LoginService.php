<?php
namespace App\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginService
{
private $authenticationUtils;

public function __construct(AuthenticationUtils $authenticationUtils)
{
$this->authenticationUtils = $authenticationUtils;
}

public function getLoginData(): array
{
// get the login error if there is one
$error = $this->authenticationUtils->getLastAuthenticationError();
// last username entered by the user
$lastUsername = $this->authenticationUtils->getLastUsername();

return [
'last_username' => $lastUsername,
'error' => $error,
];
}
}
