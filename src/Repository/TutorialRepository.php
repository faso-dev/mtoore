<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Tutorial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tutorial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tutorial[]    findAll()
 * @method Tutorial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutorialRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    const TAKE_ITEMS = 10;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Tutorial::class);
        $this->paginator = $paginator;
    }

    /**
     * @param Category $category
     * @param int|null $page
     * @return PaginationInterface
     */
    public function paginate(Category $category, ?int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('t')
                ->andWhere('t.category = :category')
                ->setParameter('category', $category)
                ->getQuery(),
            $page,
            self::TAKE_ITEMS
        );
    }
}
