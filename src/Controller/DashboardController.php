<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller
 */
class DashboardController extends AbstractController
{
    /**
     * @var TutorialRepository
     */
    private $tur;
    /**
     * @var CategoryRepository
     */
    private $catr;

    public function __construct(TutorialRepository $tur, CategoryRepository $catr)
    {
        $this->tur = $tur;
        $this->catr = $catr;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Route("/")
     */
    public function index()
    {
        return $this->render('dashboard/index.html.twig', [
            'categories' => $this->catr->count([]),
            'tutoriels' => $this->tur->count([]),
        ]);
    }
}
