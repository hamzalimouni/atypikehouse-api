<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $passwordEncoder;
    private $userRepository;

    public function  __construct(
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    public function __invoke(User $user)
    {
        if ($this->userRepository->findByEmail($user->getEmail())) {
            throw new BadRequestHttpException("Votre adresse e-mail est déjà utilisée");
        }
        $this->validator->validate($user);
        $passWord = $this->passwordEncoder->hashPassword($user, $user->getPassword());
        $user->setPassword($passWord);
        return $user;
    }
}
