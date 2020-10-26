<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\Botman\FacebookBotmanService;
use App\Service\Botman\FacebookPersistentMenuService;
use App\Service\Botman\Provider\TutorialProvider;
use App\Service\Botman\TutorialBotService;
use BotMan\BotMan\BotMan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class BotController
 * @package App\Controller
 */
class BotController extends AbstractController
{
    /**
     * @var FacebookBotmanService
     */
    private $botmanService;
    /**
     * @var BotMan
     */
    private $botman;
    /**
     * @var CategoryRepository
     */
    private $catr;

    /**
     * BotController constructor.
     * @param FacebookBotmanService $botmanService
     * @param CategoryRepository $catr
     */
    public function __construct(FacebookBotmanService $botmanService, CategoryRepository $catr)
    {
        $this->botmanService = $botmanService;
        $this->botman = $botmanService->create();
        $this->catr = $catr;
    }

    /**
     * @Route("/bot", name="bot", methods={"GET", "POST"})
     * @param TutorialProvider $provider
     * @return Response
     */
    public function index(TutorialProvider $provider)
    {

        $this->botman->hears(
            'Hello|Hi|GET_STARTED',
            function (BotMan $bot) {
                $bot->reply(
                    $this->welcome()
                );
            }
        );

        $this->botman->hears('category_{choice}', function (BotMan $bot, string $choice) use ($provider) {
            /** @var Category $category */
            $category = $this->catr->find((int)$choice);
            TutorialBotService::handle($provider, $bot, $category);
        });

        $this->botman->hears('pagination_{choice}_{page}', function (BotMan $bot, string $choice, string $page) use($provider) {
            /** @var Category $category */
            $category = $this->catr->find((int)$choice);
            $page = (int)$page;
            TutorialBotService::handle($provider, $bot, $category, $page);

        });
        $this->botman->listen();
        return new Response();
    }

    /**
     * @return string
     */
    private function welcome()
    {
        return sprintf("Welcome to Mtoore, we are here to help you understand the concepts that revolve around augmented reality with SPARK AR.\nChoose a category in the menu to consult the list of tutorials");
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @param FacebookPersistentMenuService $menuService
     * @Route("/dashboard/persistent/menu", name="persist_menu", methods={"GET"})
     * @throws TransportExceptionInterface
     */
    public function setPersistentMenu(CategoryRepository $categoryRepository, FacebookPersistentMenuService $menuService)
    {
        $response = $menuService->persistentMenu($categoryRepository->findAll());
        if ($response) {
            $this->addFlash('success', 'Vous avez mis à jour le menu des tutoriels');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour');
        }

        return $this->redirectToRoute('dashboard');
    }
}
