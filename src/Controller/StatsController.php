<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Validator\ValidatorInterface;
use App\Repository\NotificationRepository;
use App\Entity\User;
use App\Repository\HouseRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class StatsController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private NotificationRepository $notificationRepository,
        private UserRepository $userRepository,
        private HouseRepository $houseRepository,
        private ReservationRepository $reservationRepository,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/api/stats', name: 'app_stats')]
    public function index(#[CurrentUser] ?User $user)
    {
        if (!$user || ($user && $user->getRoles()[0] !== "ROLE_ADMIN"))
            throw new AccessDeniedHttpException('Access denied');

        $users = $this->userRepository->findAll();
        $houses = $this->houseRepository->findAll();
        $housesInReview = $this->houseRepository->findBy(['status' => 'UNDER_REVIEW']);
        $reservations = $this->reservationRepository->findAll();
        $reservationsAnnules = $this->reservationRepository->findBy(['status' => 'CANCELED']);
        $hotes = [];
        foreach ($houses as $house) {
            array_push($hotes, $house->getOwner()->getId());
        }

        return new JsonResponse([
            'users' => count($users),
            'users_hotes' => count(array_unique($hotes)),
            'reservations_all' => count($reservations),
            'reservations_canceled' => count($reservationsAnnules),
            'houses_all' => count($houses),
            'houses_pending' => count($housesInReview)
        ]);
    }
}
