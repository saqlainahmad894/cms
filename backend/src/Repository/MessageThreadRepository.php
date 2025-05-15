<?php

namespace App\Repository;

use App\Entity\MessageThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageThread::class);
    }

    public function findRecentMessagesWithSenders(int $limit = 5): array
{
    return $this->createQueryBuilder('m')
        ->select('m.message', 'm.sentAt', 's.email AS senderEmail') // fetch sender name/email
        ->join('m.sender', 's')
        ->orderBy('m.sentAt', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getArrayResult();
}

}
