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

/**
 * Class TutorialProvider
 * @package App\Service\Botman\Provider
 */
class TutorialProvider
{
    /**
     * @var TutorialRepository
     */
    private $tutr;

    /**
     * TutorialProvider constructor.
     * @param TutorialRepository $tutr
     */
    public function __construct(TutorialRepository $tutr)
    {
        $this->tutr = $tutr;
    }

    /**
     * @param Category $category
     * @return Tutorial[]
     */
    public function handle(Category $category)
    {
        return $this->tutr->findBy([
            'category' => $category
        ]);
    }
}