<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\Commande;
use App\Form\BookType;
use App\Repository\BooksRepository;
use App\Repository\CommandeRepository;
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

    #[Route('/admin/orders', name: 'admin_manage_orders')]
    public function manageOrders(CommandeRepository $commandeRepository): Response
    {
        $orders = $commandeRepository->findAll();
        return $this->render('admin/manage_orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/admin/orders/delete', name: 'admin_order_delete')]
    public function deleteOrder(Request $request, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): Response
    {
        $bookid = $request->query->get('bookid');
        $userid = $request->query->get('userid');

        if ($bookid && $userid) {
            $order = $commandeRepository->findOneBy([
                'bookid' => $bookid,
                'userid' => $userid
            ]);

            if ($order) {
                $entityManager->remove($order);
                $entityManager->flush();
                return $this->redirectToRoute('admin_manage_orders');
            } else {
                $this->addFlash('error', 'Error deleting order.');
            }
        }

        return $this->redirectToRoute('admin_manage_orders');
    }
}
