<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Repository\NotificationRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private NotificationRepository $notificationRepository,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(#[CurrentUser] ?User $user, Request $request): JsonResponse
    {

        if (count($user->getHouses()) > 0 || $user->getRoles()[0] === 'ROLE_ADMIN') {
            $notifications = $this->notificationRepository->findProprietaireNotifications($user);
            $data = $this->serializer->serialize($notifications, JsonEncoder::FORMAT);
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        } else {
            $notifications = $this->notificationRepository->findUserNotifications($user);
            $data = $this->serializer->serialize($notifications, JsonEncoder::FORMAT);
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        }
    }
}
