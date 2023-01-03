<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Disponibility;
use App\Entity\Equipement;
use App\Entity\House;
use App\Entity\Image;
use App\Entity\Notification;
use App\Entity\Propriety;
use App\Entity\ProprietyValue;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\DisponibilityRepository;
use App\Repository\EquipementRepository;
use App\Repository\HouseRepository;
use App\Repository\ProprietyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;

#[AsController]
class HouseController extends AbstractController
{

    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private CategoryRepository $categoryRepository,
        private EquipementRepository $equipementRepository,
        private ProprietyRepository $proprietyRepository,
        private HouseRepository $houseRepository,
        private AddressRepository $addressRepository,
        private DisponibilityRepository $disponibilityRepository,
    ) {
    }

    public function __invoke(Request $request): House
    {
        // dd($request->request->get('id'));
        // $houseDispo = $this->disponibilityRepository->findBy(['House' => 525]);
        // foreach ($houseDispo as $h) {
        //     dd( $h);
        // }

        if ($request->isMethod('POST')) {

            $house = new House();
            $address = new Address();

            if ($request->request->get('id')) {

                $house = $this->houseRepository->find($request->request->get('id'));
                foreach ($house->getDisponibilities() as $d) {
                    $house->removeDisponibility($d);
                }
                foreach ($house->getEquipments() as $e) {
                    $house->removeEquipment($e);
                }
                foreach ($house->getProperties() as $p) {
                    $house->removeProperty($p);
                }
                foreach ($house->getImages() as $i) {
                    $house->removeImage($i);
                }
            } else {
                $house->setOwner($this->security->getUser());
            }

            $adr = json_decode($request->request->get('address'));
            $address->setAddress($adr->address)
                ->setCity($adr->city)
                ->setZipcode($adr->zipcode)
                ->setCountry($adr->country)
                ->setLongitude($adr->longitude ?: 0)
                ->setLatitude($adr->latitude ?: 0);
            $house->setTitle($request->request->get('title'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setNbPerson($request->request->get('nbPerson'))
                ->setSurface($request->request->get('surface'))
                ->setBeds($request->request->get('beds'))
                ->setRooms($request->request->get('rooms'))
                ->setAddress($address)
                ->setStatus("UNDER_REVIEW")
                ->setCategory($this->categoryRepository->findBy(['id' => $request->request->get('category')])[0]);


            foreach (json_decode($request->request->get('disponibilities')) as $indispo) {
                $d = new Disponibility();
                $d->setDate(new \DateTime($indispo));
                $house->addDisponibility($d);
                $this->entityManager->persist($d);
            }

            foreach (json_decode($request->request->get('equipments')) as $equipment) {
                $house->addEquipment(
                    $this->equipementRepository
                        ->findBy(
                            ['id' => $equipment]
                        )[0]
                );
            }


            foreach (json_decode($request->request->get('properties')) as $id => $value) {
                if ($value != "" || $value == true) {
                    $p = new ProprietyValue();
                    $p->setHouse($house);
                    $p->setPropriety($this->proprietyRepository
                        ->findBy(
                            ['id' => $id]
                        )[0]);
                    $p->setValue($value);
                    $this->entityManager->persist($p);
                }
            }

            foreach ($request->files->all("images") as $file) {
                $image = new Image();
                $image->setFile($file);
                $image->setFilePath($this->getParameter('kernel.project_dir') . '\public\media');
                $this->validator->validate($image);
                $house->addImage($image);
                $this->entityManager->persist($image);
            }

            if ($request->request->get('id')) {
                $this->entityManager->flush($house);
            }

            return $house;
        } else if ($request->isMethod('GET')) {
            $house = $request->get('data');
            if ($house?->status == "APPROVED" || $house?->owner === $this->security->getUser() || $this->security->getUser() && in_array('ROLE_ADMIN', $this->security->getUser()?->getRoles(), true)) {
                return $house;
            } else {
                throw new AccessDeniedHttpException('Access denied');
            }
        } else if ($request->isMethod('PATCH')) {

            $house = $request->get('data');
            if ($this->security->getUser() && in_array('ROLE_ADMIN', $this->security->getUser()?->getRoles(), true)) {
                $notification = new Notification();
                $notification->setUserId($house->getOwner());
                if ($house?->status == "APPROVED") {
                    $notification->setType('ANNONCE_APPROVED');
                    $notification->setContent('Votre annonce a été acceptée par notre équipe.');
                } else if ($house?->status == "REJECTED") {
                    $notification->setType('ANNONCE_REJECTED');
                    $notification->setContent('Votre annonce a été refusée par notre équipe.');
                }
                $notification->setCreatedAt(new DateTimeImmutable());
                $notification->setData($house->getId());
                $this->entityManager->persist($notification);
            }
            return $house;
        }
    }
}
