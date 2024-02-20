<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function findDialogMessages(User $currentUser, User $selectedUser): array
    {
        $qb = $this->createQueryBuilder('m');

        $qb
            ->where('m.sender = :currentUser AND m.recipient = :selectedUser')
            ->orWhere('m.sender = :selectedUser AND m.recipient = :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('selectedUser', $selectedUser)
            ->orderBy('m.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
