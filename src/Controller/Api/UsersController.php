<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\Users;
use App\DTO\UserDTO;
use App\DTO\UsersImportDTO;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private Serializer $serializer;
    private RecursiveValidator $validator;

    public function __construct(EntityManagerInterface $entityManager) {
        $encoders = [new CsvEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(), new ArrayDenormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->entityManager = $entityManager;    
        $this->validator = (new ValidatorBuilder())
            ->enableAnnotationMapping()
            ->getValidator();
    }

    #[Route('/api', name: 'app_api_users', methods: ['GET'])]
    public function index(): JsonResponse {
        return $this->json($this->entityManager->getRepository(Users::class)->getLastAddedUsers());
    }

    #[Route('/api/import', name: 'app_api_users_import', methods: ['POST'])]
    public function import(Request $request): JsonResponse {
        $dtoArray = $this->serializer->deserialize($request->getContent(), UserDTO::class.'[]', 'csv');
        $totalCount = count($dtoArray);
        $failedImport = []; 
        $batch = [];
        $users = [];

        for($i = 0; $i < count($dtoArray); $i++) {
            $errors = $this->validator->validate($dtoArray[$i]);

            if(count($errors) > 0) {
                $failedImport[] = $dtoArray[$i]->email;
                continue;
            }

            $users []= $dtoArray[$i];
            $batch []= $dtoArray[$i];

            if(count($batch) >= 10000 || $i == count($dtoArray) - 1) {
                $this->entityManager->getRepository(Users::class)->createUsersByBatch($batch);
                $batch = [];
            }
        }

        return $this->json([
            "status" => 'ok',
            "code" => 200,
            "totalCount" => $totalCount,
            "failedImport" => $failedImport,
            "countOfImported" => count($users)
        ]);
    }


    #[Route('/api/{id}', name: 'app_api_users_get_by_id', methods: ['GET'])]
    public function getById(int $id): JsonResponse {
        return $this->json( $this->entityManager->getRepository(Users::class)->getById($id));
    }

    #[Route('/api/{id}', name: 'app_api_users_delete', methods: ['DELETE'])]
    public function deleteUserById(int $id): JsonResponse {
        return $this->json( $this->entityManager->getRepository(Users::class)->deleteUser($id));
    }

    #[Route('/api', name: 'app_api_users_create_one', methods: ['POST'])]
    public function createOne(Request $request): JsonResponse {
        $dto = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');       
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }
        
        $user = $this->entityManager->getRepository(Users::class)->createUser($dto);
        return $this->json($user);
    }
}
