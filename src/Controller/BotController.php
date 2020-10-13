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
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    public function index()
    {

        $this->botman->hears(
            'Salut|Coucou|cc|Bonjour|Slt|Bonsoir',
            function (BotMan $bot) {
                $bot->reply(
                    $this->buildConversationButtons()
                );
            }
        );

        $this->botman->hears('facemask', function (BotMan $botMan) {
            $botMan->reply("Vous avez choisi Face Mask");
        });
        $this->botman->hears('segmentation', function (BotMan $botMan) {
            $botMan->reply("Vous avez choisi Face Segmentation");
        });
        $this->botman->hears('makeup', function (BotMan $botMan) {
            $botMan->reply("Vous avez choisi Face Makeup");
        });

        $this->botman->listen();

        return new Response();
    }

    private function buildConversationButtons()
    {

        $categories = $this->catr->findAll();
        $btnTemplate = ButtonTemplate::create("Bienvenue sur MTOORE, vote bot pour apprendre la réalité augmentée");

        foreach ($categories as $category) {
            $btnTemplate->addButton(
                ElementButton::create($category->getTitle())
                    ->type('postback')
                    ->payload(strtolower(str_replace(' ', '', $category->getTitle())))
            );
        }

        return
            $btnTemplate;
    }
}
