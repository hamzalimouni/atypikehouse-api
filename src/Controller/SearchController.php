<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Validator\ValidatorInterface;
use App\Repository\NotificationRepository;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\HouseRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class SearchController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private NotificationRepository $notificationRepository,
        private UserRepository $userRepository,
        private HouseRepository $houseRepository,
        private AddressRepository $addressRepository,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/api/search', name: 'app_search')]
    public function index(Request $request)
    {
        $addressRepo = $this->addressRepository->findAddresses();
        $addresses = array();
        foreach ($addressRepo as $address) {
            array_push($addresses, ['city' => $address->getCity(), 'country' => $address->getCountry()]);
        }
        return new JsonResponse($addresses);
    }
}
