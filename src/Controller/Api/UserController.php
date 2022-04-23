<?php

namespace App\Controller\Api;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('', methods: 'GET')]
    public function getAll(): JsonResponse
    {
        $data = [];
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $data[] = $user->toArray();
        }

        return $this->json($data);
    }

    #[Route('', methods: 'POST')]
    public function create(Request $request, EntityManagerInterface $entity, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $user = UserFactory::create($request->get('email'));
        $user->setPassword($userPasswordHasher->hashPassword($user, $request->get('password')));

        try {
            $entity->persist($user);
            $entity->flush();
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            return $this->json([
                'status' => 'error',
                'message' => 'The user already exists',
            ]);
        }

        return $this->json([
            'status' => 'OK',
        ]);
    }
}
