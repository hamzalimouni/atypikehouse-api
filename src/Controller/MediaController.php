<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class MediaController extends AbstractController
{

    public function __construct(
        private ImageRepository $imageRepository,
    ) {
    }

    // #[Route('/media/images/{img}', name: 'app_media')]
    public function __invoke(Request $request)
    {
        // dd($request->get('id'));
        $image = $this->imageRepository->findBy(['id' => $request->get('id')])[0];
        $filepath = $this->getParameter('kernel.project_dir') . "/public/media/" . $image->getFileName();

        $response = new Response(file_get_contents($filepath));

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $image->getFileName());

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Length', filesize($filepath));
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
