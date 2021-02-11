<?php

namespace App\Repository;

use App\Entity\DonneeEconomique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DonneeEconomique|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonneeEconomique|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonneeEconomique[]    findAll()
 * @method DonneeEconomique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneeEconomiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonneeEconomique::class);
    }

    public function findMontantPretsEtLocationByProduct(int $id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
                SELECT produit.id, produit.nom, WEEK(donnee_economique.date) As Semaine, SUM(donnee_economique.prix_location) as "Somme Location", SUM(donnee_economique.prix_rente) as "Somme Rente" FROM donnee_economique
                JOIN produit ON produit.id = donnee_economique.produit_id
                WHERE produit.id = :id
                GROUP BY WEEK(donnee_economique.date)
                ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }



    // /**
    //  * @return DonneeEconomique[] Returns an array of DonneeEconomique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DonneeEconomique
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
