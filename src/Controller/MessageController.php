<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class MessageController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private UserRepository $userRepository,
        private MessageRepository $messageRepository,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(#[CurrentUser] ?User $user, Request $request)
    {
        $notification = new Notification();
        $message = $request->get('data');
        if ($request->isMethod('POST')) {
            $message = new Message();
            $message->setSender($this->security->getUser());
            $message->setReceiver($request->get('data')->getReceiver());
            $message->setContent($request->get('data')->getContent());
            $notification->setUserId($request->get('data')->getReceiver());
            $notification->setType('NEW_MESSAGE');
            $notification->setData($user->getId());
            $notification->setContent('Vous avez reçu un nouveau message.');
            $notification->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($notification);
            return $message;
        } else if ($request->isMethod('GET')) {
            if ($request->query->get('user')) {
                return $this->messageRepository->findUserConversation($user, $request->query->get('user'));
            } else {
                $messages = $this->messageRepository->findUserMessages($user);
                $users = [];
                foreach ($messages as $message) {
                    array_push($users, $message->getSender());
                    array_push($users, $message->getReceiver());
                }
                return array_unique($users, SORT_REGULAR);
            }
        }
        // else if ($request->isMethod('PATCH')) {
        //     $notification->setUserId($reservation->getUser());
        //     $notification->setType('RESERVATION_CANCELED');
        //     $notification->setData($reservation->getId());
        //     $notification->setContent('Votre réservation a été annulée par l\'hôte');
        // }
        // $notification->setCreatedAt(new DateTimeImmutable());
        // $this->entityManager->persist($notification);
    }
}
