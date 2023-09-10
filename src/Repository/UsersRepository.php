<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\DTO\UserDTO;

/**
 * @extends ServiceEntityRepository<Users>
 *
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Users::class);
    }

    public function createUsersByBatch(array $usersDTO): void {
        $users = [];
        foreach($usersDTO as $userDTO) {
            $user = $this->findUserByEmail($userDTO->email);

            if($user) {
                $user->setIsValid(true);
                $user->setName($userDTO->firstName);
                $user->setLastName($userDTO->lastName);
            } else {
                $user = new Users();
                $user->setName($userDTO->firstName);
                $user->setLastName($userDTO->lastName);
                $user->setEmail($userDTO->email);    
            }        

            $this->getEntityManager()->persist($user);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function createUser(UserDTO $userDTO): Users {
        $user = $this->findUserByEmail($userDTO->email);

        if($user) {
            $user->setIsValid(true);
            $user->setName($userDTO->firstName);
            $user->setLastName($userDTO->lastName);
        } else {
            $user = new Users();
            $user->setName($userDTO->firstName);
            $user->setLastName($userDTO->lastName);
            $user->setEmail($userDTO->email);    
        }        

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush($user);
        return $user;
    }

    private function findUserByEmail(string $email): ?Users {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function deleteUser(int $id): bool {
        $user = $this->getById($id);

        if(!$user) {
            return false;
        }

        $user->setIsValid(false);
        $this->getEntityManager()->flush($user);
        return true;
    }

    public function getAllValidUsers(): array {
        return $this->createQueryBuilder('u')
            ->andWhere('u.is_valid = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getLastAddedUsers(int $count = 25): array {
        return $this->createQueryBuilder('u')
            ->andWhere('u.is_valid = :val')
            ->setParameter('val', true)
            ->orderBy('u.id', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

   public function getById(int $id): ?Users {
       return $this->createQueryBuilder('u')
           ->andWhere('u.id = :val')
           ->andWhere('u.is_valid = true')
           ->setParameter('val', $id)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
