<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Cloner\Data;

class UserUpdateController extends AbstractController
{
    private $passwordEncoder;

    public function  __construct(
        UserPasswordHasherInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    public function __invoke(User $user)
    {
        $this->validator->validate($user);
        $passWord = $this->passwordEncoder->hashPassword($user, $user->getPassword());
        $user->setPassword($passWord);
        return $user;
    }
}
