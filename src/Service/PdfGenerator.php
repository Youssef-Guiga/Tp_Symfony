<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGenerator
{
private $twig;

public function __construct(Environment $twig)
{
$this->twig = $twig;
}

public function generatePdf($template, $data)
{
// Configure Dompdf according to your needs
$pdfOptions = new Options();
$pdfOptions->set('defaultFont', 'Arial');

// Instantiate Dompdf with our options
$dompdf = new Dompdf($pdfOptions);

// Retrieve the HTML generated in our twig file
$html = $this->twig->render($template, $data);

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser (force download)
$output = $dompdf->output();
return new Response($output, 200, [
'Content-Type' => 'application/pdf',
'Content-Disposition' => 'inline; filename="cart.pdf"'
]);
}
}
