<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Factory\AddressBookFactory;
use App\Repository\AddressBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/address_books/{user}')]
class AddressBookController extends AbstractController
{
    #[Route('', methods: 'GET')]
    public function getAll(User $user, AddressBookRepository $addressBookRepository): JsonResponse
    {
        $data = [];

        foreach ($addressBookRepository->findAll() as $addressBook) {
            $data = $addressBook->toArray();
        }

        return $this->json($data);
    }

    #[Route('', methods: 'POST')]
    public function create(User $user, Request $request, EntityManagerInterface $entity): JsonResponse
    {
        if ('' === $request->get('first_name')) {
            return $this->json([
                'status' => 'error',
                'message' => 'The required fields (first_name) cannot be empty',
            ]);
        }

        $addressBook = AddressBookFactory::create(
            $user,
            $request->get('first_name'),
            $request->get('last_name'),
            $request->get('email'),
            $request->get('phone')
        );

        $entity->persist($addressBook);
        $entity->flush();

        return $this->json(['status' => 'OK']);
    }
}
