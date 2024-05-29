<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BookType;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(BooksRepository $booksRepository): Response
    {
        $books = $booksRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/admin/books/new', name: 'admin_book_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Books();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $fileName);
                $book->setImg('uploads/' . $fileName);
            }

            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/books/delete/{id}', name: 'admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, BooksRepository $booksRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $book = $booksRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
