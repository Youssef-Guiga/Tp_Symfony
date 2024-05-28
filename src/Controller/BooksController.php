<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Books;
use App\Form\BookType;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
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
#[Route('/books/new', name: 'book_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $book = new Books();
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }

    return $this->render('book/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/books/delete/{id}', name: 'book_delete')]
public function delete(Books $book, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($book);
    $entityManager->flush();

    return $this->redirectToRoute('book_index');
}

}
