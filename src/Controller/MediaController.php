<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class MediaController extends AbstractController
{
    #[Route('/media/images/{img}', name: 'app_media')]
    public function index(string $img): Response
    {
        $filepath = $this->getParameter('kernel.project_dir') . "\public\media\\" . $img;

        $response = new Response(file_get_contents($filepath));

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $img);

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Length', filesize($filepath));
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
