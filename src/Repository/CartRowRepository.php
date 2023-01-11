<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartRow>
 *
 * @method CartRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartRow[]    findAll()
 * @method CartRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartRow::class);
    }

    public function save(CartRow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartRow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function insertOrUpdateDuplicateRow(CartRow $cartRow)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
           INSERT INTO `cart_row`(`cart_id`, `dish_id`, `quantity`, `unit_price`)
           VALUES (:cart_id, :dish_id, :quantity, :price)
           ON DUPLICATE KEY UPDATE quantity = quantity + :quantity;
        ';

        $params = [
            'cart_id'  => $cartRow->getCart()->getId(),
            'dish_id'  => $cartRow->getDish()->getId(),
            'quantity' => $cartRow->getQuantity(),
            'price'    => $cartRow->getUnitPrice()
        ];

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);
    }

//    /**
//     * @return CartRow[] Returns an array of CartRow objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CartRow
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
