<?php
namespace App\Controller;

use App\Entity\Books;
use App\Repository\BooksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
#[Route('/', name: 'book_index')]
public function index(BooksRepository $bookRepository): Response
{
$books = $bookRepository->findAll();

return $this->render('book/index.html.twig', [
'books' => $books,
]);
}


}
