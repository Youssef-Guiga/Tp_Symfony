<?php



namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/aboutus', name: "aboutus_")]
class AboutusController extends AbstractController
{

#[Route('/', name: "index")]
public function index()
{
        // Render the about us page
        return $this->render('aboutus/index.html.twig');
}}
