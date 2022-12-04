<?php

namespace App\Controller\Api;

use App\Entity\AddressBook;
use App\Factory\AddressBookFactory;
use App\Repository\AddressBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/address_book')]
class AddressBookController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'add_address', methods: 'POST')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $requiredFields = ['firstName'];
        foreach ($requiredFields as $requiredField) {
            if (empty($requiredField)) {
                return $this->json([
                    'status' => 'error',
                    'message' => sprintf('The required fields %s cannot be empty.', $requiredField),
                ]);
            }
        }

        $addressBook = AddressBookFactory::create($request->request->all());
        $entityManager->persist($addressBook);
        $entityManager->flush();

        return $this->json(['status' => 'Address Book record created!'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_one_address_book_record', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $addressBook = $this->entityManager->getRepository(AddressBook::class)->findOneBy([
            'id' => $id,
        ]);

        if (null !== $addressBook) {
            return $this->json($addressBook->toArray(), Response::HTTP_OK);
        }

        return $this->json('No records found!');
    }

    #[Route('/all', name: 'get_all_address_book_records', methods: ['GET'])]
    public function getAll(AddressBookRepository $addressBookRepository): JsonResponse
    {
        $data = [];

        foreach ($addressBookRepository->findAll() as $addressBook) {
            $data[] = $addressBook->toArray();
        }

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update_address_book', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $addressBook = $this->entityManager->getRepository(AddressBook::class)->findOneBy([
            'id' => $id,
        ]);

        if (null !== $addressBook) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            foreach ($request->request->all() as $parameter => $value) {
                try {
                    $propertyAccessor->setValue($addressBook, $parameter, $value);
                } catch (NoSuchPropertyException $noSuchPropertyException) {
                }
            }
            $this->entityManager->flush();
        }

        return $this->json($addressBook->toArray());
    }

    #[Route('/{id}', name: 'delete_address_book', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $addressBook = $this->entityManager->getRepository(AddressBook::class)->findOneBy([
            'id' => $id,
        ]);

        if (null !== $addressBook) {
            $this->entityManager->remove($addressBook);
            $this->entityManager->flush();
        }

        return $this->json(['status' => 'Address book record deleted.'], Response::HTTP_NO_CONTENT);
    }
}
