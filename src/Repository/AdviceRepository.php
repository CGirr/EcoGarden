<?php

namespace App\Repository;

use App\Entity\Advice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advice>
 */
class AdviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advice::class);
    }

    /**
     * @throws Exception
     */
    public function getAdvicesOfTheMonth(): array
   {
       $conn = $this->getEntityManager()->getConnection();
       $month = (int) (new \DateTime())->format('n');

       $sql = 'SELECT * FROM advice WHERE months @> :month::jsonb';

       return $conn->executeQuery($sql, [
           'month' => json_encode([$month])
       ])->fetchAllAssociative();
   }

    /**
     * @throws Exception
     */
    public function getAdvicesByMonth($month): array
   {
       $conn = $this->getEntityManager()->getConnection();

       $sql = 'SELECT * FROM advice WHERE months @> :month::jsonb';

       return $conn->executeQuery($sql, [
           'month' => json_encode([$month])
       ])->fetchAllAssociative();
   }
}
