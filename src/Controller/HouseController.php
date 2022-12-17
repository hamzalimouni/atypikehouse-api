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
use App\Repository\CategoryRepository;
use App\Repository\EquipementRepository;
use App\Repository\ProprietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    ) {
    }

    public function __invoke(Request $request): House
    {
        if ($request->isMethod('post')) {
            $adr = json_decode($request->request->get('address'));
            $address = new Address();
            $address->setAddress($adr->address)
                ->setCity($adr->city)
                ->setZipcode($adr->zipcode)
                ->setCountry($adr->country)
                ->setLongitude($adr->longitude ?: 0)
                ->setLatitude($adr->latitude ?: 0);
            $house = new House();
            $house->setTitle($request->request->get('title'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setNbPerson($request->request->get('nbPerson'))
                ->setSurface($request->request->get('surface'))
                ->setBeds($request->request->get('beds'))
                ->setRooms($request->request->get('rooms'))
                ->setAddress($address)
                ->setOwner($this->security->getUser())
                ->setStatus("NEW_LISTING")
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
                $p = new ProprietyValue();
                $p->setHouse($house);
                $p->setPropriety($this->proprietyRepository
                    ->findBy(
                        ['id' => $id]
                    )[0]);
                $p->setValue($value);
                $this->entityManager->persist($p);
            }

            foreach ($request->files->all("images") as $file) {
                $image = new Image();
                $image->setFile($file);
                $image->setFilePath($this->getParameter('kernel.project_dir') . '\public\media');
                $this->validator->validate($image);
                $house->addImage($image);
                $this->entityManager->persist($image);
            }
            return $house;
        } else if ($request->isMethod('get')) {
            $house = $request->get('data');
            if ($house?->status == "APPROVED" || $house?->owner === $this->security->getUser() || $this->security->getUser() && in_array('ROLE_ADMIN', $this->security->getUser()?->getRoles(), true)) {
                return $house;
            } else {
                throw new AccessDeniedHttpException('Access denied');
            }
        }
    }
}
