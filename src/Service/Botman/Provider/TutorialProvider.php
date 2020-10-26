<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Service\Botman\Provider;

use App\Entity\Category;
use App\Entity\Tutorial;
use App\Repository\TutorialRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class TutorialProvider
 * @package App\Service\Botman\Provider
 */
class TutorialProvider
{
    /**
     * @var TutorialRepository
     */
    private $tur;

    /**
     * TutorialProvider constructor.
     * @param TutorialRepository $tur
     */
    public function __construct(TutorialRepository $tur)
   {
       $this->tur = $tur;
   }

    /**
     * @param Category|int $category
     * @param int|null $page
     * @return PaginationInterface
     */
    public function handle($category, ?int $page = 1): PaginationInterface
    {
        return $this->tur->paginate($category, $page);
   }
}