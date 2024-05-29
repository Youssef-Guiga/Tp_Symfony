<?php
namespace App\Controller;

use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\BooksRepository;

#[Route('/cart/pdf')]
class PdfController extends AbstractController
{
private $pdfGenerator;

public function __construct(PdfGenerator $pdfGenerator)
{
$this->pdfGenerator = $pdfGenerator;
}

#[Route('/', name: 'cart_pdf_generate')]
public function cartPdf(SessionInterface $session, BooksRepository $booksRepository)
{
// Get the cart
$cart = $session->get('cart', []);

// Initialize an array to hold our full cart details
$cartItems = [];
$total = 0;

foreach ($cart as $id => $quantity) {
$book = $booksRepository->find($id);
if ($book) {
$cartItems[] = [
'book' => $book,
'quantity' => $quantity
];
$total += $book->getPrice() * $quantity;
}
}

// Generate the PDF
return $this->pdfGenerator->generatePdf('cart/pdf.html.twig', [
'cartItems' => $cartItems,
'total' => $total
]);
}
}
