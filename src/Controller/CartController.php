<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/cart', name: "cart_")]
class CartController extends AbstractController
{
    #[Route('/', name: "index")]
    public function index(SessionInterface $session, BooksRepository $booksRepository)
    {
        // Get the cart
        $cart = $session->get('cart', []);

        // Initialize an array to hold our full cart details
        $cartData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $book = $booksRepository->find($id);
            if ($book) {
                $cartData[] = [
                    'book' => $book,
                    'quantity' => $quantity
                ];
                $total += $book->getPrice() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', compact('cartData', 'total'));
    }

    #[Route('/add/{id}', name: "add")]
    public function add($id, SessionInterface $session, BooksRepository $booksRepository)
    {
        // Get the book from the repository
        $book = $booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        // Get the cart
        $cart = $session->get('cart', []);

        // Add the book to the cart
        if (empty($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }
        $session->set('cart', $cart);

        // Redirect to the cart page
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: "remove")]
    public function remove($id, SessionInterface $session, BooksRepository $booksRepository)
    {
        // Get the book from the repository
        $book = $booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        // Get the cart
        $cart = $session->get('cart', []);

        // Remove the book from the cart
        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }
        $session->set('cart', $cart);

        // Redirect to the cart page
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: "delete")]
    public function delete($id, SessionInterface $session, BooksRepository $booksRepository)
    {
        // Get the book from the repository
        $book = $booksRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        // Get the cart
        $cart = $session->get('cart', []);

        // Remove the book from the cart
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        // Redirect to the cart page
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/empty', name: "empty")]
    public function empty(SessionInterface $session)
    {
        $session->remove('cart');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/order', name: "order")]
    public function order(Request $request, SessionInterface $session, BooksRepository $booksRepository, EntityManagerInterface $em)
    {
        $orderForm = $this->createForm(OrderType::class);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $data = $orderForm->getData();
            $user = $this->getUser();
            $cart = $session->get('cart', []);

            foreach ($cart as $id => $quantity) {
                $book = $booksRepository->find($id);
                if ($book) {
                    $order = new Commande();
                    $order->setBookid($book); // Set the Books entity
                    $order->setUserid($user); // Set the User entity
                    $order->setVille($data['ville']);
                    $order->setQuantity($quantity);
                    $em->persist($order);
                }
            }

            $em->flush();
            $session->remove('cart');

            $this->addFlash('success', 'Your order has been placed successfully!');
            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cart/order.html.twig', [
            'orderForm' => $orderForm->createView(),
        ]);
    }
}
