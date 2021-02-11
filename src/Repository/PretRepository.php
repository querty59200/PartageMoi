<?php

namespace App\Repository;

use App\Entity\Pret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pret[]    findAll()
 * @method Pret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pret::class);
    }

    public function findMontantTotalPretsForLastWeek()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT SUM(pret.montant_rente) as "montant" FROM pret
                WHERE pret.date_debut > DATE(NOW()) - INTERVAL 7 DAY
                ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function findPretsByCategorieByDateDebutForLastWeek()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT pret.date_debut as dateDebut, categorie.nom, SUM(pret.montant_rente) as montant FROM pret
                JOIN produit ON produit.id = pret.produit_id
                JOIN categorie ON categorie.id = produit.categorie_id
                WHERE pret.date_debut > DATE(NOW()) - INTERVAL 7 DAY
                GROUP BY pret.date_debut, categorie.nom
                ORDER BY pret.date_debut DESC
                ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    // /**
    //  * @return Pret[] Returns an array of Pret objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pret
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
