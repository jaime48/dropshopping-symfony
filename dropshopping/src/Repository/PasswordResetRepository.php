<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Support\Str;
//use Symfony\Bundle\MakerBundle\Str;

/**
 * @method PasswordReset|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordReset|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordReset[]    findAll()
 * @method PasswordReset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetRepository extends ServiceEntityRepository
{

    const PASSWORD_RESET_KEY = 'password-reset';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function generatePasswordResetLink($email) {
        $token = hash_hmac('sha256',$email.self::PASSWORD_RESET_KEY.random_bytes(80), $_ENV['APP_SECRET']);
        return $token;

    }

    public function deleteByEmail($value): ? bool
    {
        return $this->createQuery('DELETE FROM password_reset e WHERE e.email = :email')
            >setParameter('email', $value)->execute()
            ;
    }

    // /**
    //  * @return PasswordReset[] Returns an array of PasswordReset objects
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
    public function findOneBySomeField($value): ?PasswordReset
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
