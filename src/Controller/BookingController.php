<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Disponibility;
use App\Entity\Equipement;
use App\Entity\House;
use App\Entity\Image;
use App\Entity\Propriety;
use App\Entity\ProprietyValue;
use App\Entity\Reservation;
use App\Repository\CategoryRepository;
use App\Repository\EquipementRepository;
use App\Repository\HouseRepository;
use App\Repository\ProprietyRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;

class BookingController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private HouseRepository $houseRepository,
    ) {
    }

    public function __invoke(Request $request): Reservation
    {
        // var_dump($request->request->all());
        $reservation = new Reservation();
        $reservation->setFromDate(new \DateTimeImmutable($request->request->get('from')));
        $reservation->setToDate(new \DateTimeImmutable($request->request->get('to')));
        $reservation->setUser($this->security->getUser());
        $reservation->setNbPersons((int)$request->request->get('travelers'));
        $reservation->setAmount((float)$request->request->get('total'));
        $reservation->setStatus('APPROVED');
        $reservation->setHouse($this->houseRepository->findBy(['id' => $request->request->get('house')])[0]);
        return $reservation;
    }
}
