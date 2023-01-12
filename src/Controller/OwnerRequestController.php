<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\OwnerRequest;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OwnerRequestController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): OwnerRequest
    {
        $data = $request->get('data');
        $notification = new Notification();
        $notification->setUserId($data->getUser());
        if ($data?->getStatus() == "APPROVED") {
            $notification->setType('OWNER_APPROVED');
            $notification->setContent("Félicitations! Nous avons examiné votre demande de devenir propriétaire et avons décidé de l'approuver. ");
        } else if ($data?->getStatus() == "REFUSED") {
            $notification->setType('OWNER_REJECTED');
            $notification->setContent("Nous sommes désolés de vous informer que votre demande pour devenir un propriétaire n'a pas été approuvée. Si vous souhaitez faire appel de cette décision, veuillez nous contacter pour vous fournir des informations supplémentaires");
        }
        $notification->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($notification);
        return $data;
    }
}
