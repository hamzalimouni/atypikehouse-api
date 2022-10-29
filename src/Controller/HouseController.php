<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Equipement;
use App\Entity\House;
use App\Entity\Image;
use App\Repository\CategoryRepository;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
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
    ) {
    }

    public function __invoke(Request $request): House
    {
        $address = new Address();
        $address->setAddress($request->request->get('address'))
            ->setCity($request->request->get('city'))
            ->setState($request->request->get('state'))
            ->setZipcode($request->request->get('zipcode'))
            ->setCountry($request->request->get('country'))
            ->setLongitude($request->request->get('longitude') ?: 0)
            ->setLatitude($request->request->get('latitude') ?: 0);
        $house = new House();
        $house->setTitle($request->request->get('title'))
            ->setDescription($request->request->get('description'))
            ->setPrice($request->request->get('price'))
            ->setNbPerson($request->request->get('nbPerson'))
            ->setSurface($request->request->get('surface'))
            ->setRooms($request->request->get('rooms'))
            ->setAddress($address)
            ->setOwner($this->security->getUser())
            ->setStatus("NEW_LISTING")
            ->setCategory($this->categoryRepository->findBy(['name' => $request->request->get('category')])[0]);


        foreach ($request->request->all('equipments') as $equipment) {
            $house->addEquipment(
                $this->equipementRepository
                    ->findBy(
                        ['name' => $equipment]
                    )[0]
            );
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
    }
}
