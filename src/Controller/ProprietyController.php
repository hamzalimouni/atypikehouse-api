<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Propriety;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProprietyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): Propriety
    {
        $propriety = $request->get('data');
        $notification = new Notification();
        if($request->isMethod('post')){
            $notification->setType('NEW');
            $notification->setContent('Une nouvelle propriété (' . $propriety->getName() . ') a été ajoutée.');
        }else if($request->isMethod('PATCH')){
            $notification->setType('EDIT');
            $notification->setContent('La propriété (' . $propriety->getName() . ') a été modifiée.');
        }else if($request->isMethod('Delete')){
            $notification->setType('DELETE');
            $notification->setContent('La propriété (' . $propriety->getName() . ') a été supprimée.');
        }
        $notification->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($notification);

        return $propriety;
    }
}
