<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Entity\Notification;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class EquipmentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): Equipement
    {
        $equipement = $request->get('data');
        $notification = new Notification();
        if($request->isMethod('post')){
            $notification->setType('NEW');
            $notification->setContent('Un nouvel équipement (' . $equipement->getName() . ') a été ajouté.');
        }else if($request->isMethod('PATCH')){
            $notification->setType('EDIT');
            $notification->setContent('L\'équipement (' . $equipement->getName() . ') a été modifié.');
        }else if($request->isMethod('Delete')){
            $notification->setType('DELETE');
            $notification->setContent('L\'équipement (' . $equipement->getName() . ') a été supprimé.');
        }
        $notification->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($notification);
        return $equipement;
    }
}
