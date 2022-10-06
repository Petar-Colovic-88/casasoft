<?php

namespace App\Repository;

use App\Entity\Description;
use App\Entity\User;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function save(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Property[] Returns an array of Property objects
    */
   public function findByManagers(
       string $role = 'ROLE_MANAGER',
       string $status = 'RENTED',
       string $city = 'Zurich',
       int $numberOfRoomsFrom = 3,
       int $numberOfRoomsTo = 5,
   ): array {
       return $this->createQueryBuilder('property')
           ->leftJoin(User::class, 'user', Join::WITH, 'property.user = user.id')
           ->andWhere('user.role = :role')
           ->setParameter('role', $role)
           ->andWhere('property.status = :status')
           ->setParameter('status', $status)
           ->andWhere('property.city = :city')
           ->setParameter('city', $city)
           ->andWhere('property.numberOfRooms BETWEEN :numberOfRoomsFrom AND :numberOfRoomsTo')
           ->setParameter('numberOfRoomsFrom', $numberOfRoomsFrom)
           ->setParameter('numberOfRoomsTo', $numberOfRoomsTo)
           ->orderBy('property.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
     * @return Property[] Returns an array of Property objects
     */
    public function findByAdmins(
        string $role = 'ROLE_ADMIN',
        string $status = 'ACTIVE',
        string $city = 'Zurich',
        array $descriptionLanguage = ['en', 'de'],
    ): array {
        return $this->createQueryBuilder('property')
            ->leftJoin(User::class, 'user', Join::WITH, 'property.user = user.id')
            ->leftJoin(Description::class, 'description', Join::WITH, 'property.id = description.property')
            ->andWhere('user.role = :role')
            ->setParameter('role', $role)
            ->andWhere('property.status = :status')
            ->setParameter('status', $status)
            ->andWhere('property.city = :city')
            ->setParameter('city', $city)
            ->andWhere('description.language IN (:language)')
            ->setParameter('language', $descriptionLanguage)
            ->orderBy('property.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
}
