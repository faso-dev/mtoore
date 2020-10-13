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
use App\Service\Botman\Provider\TutorialProvider;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
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
     * @param TutorialProvider $provider
     * @return Response
     */
    public function index(TutorialProvider $provider)
    {

        $this->botman->hears(
            'Salut|Coucou|cc|Bonjour|Slt|Bonsoir',
            function (BotMan $bot) {
                $bot->reply(
                    $this->buildConversationButtons()
                );
            }
        );

        //$categories = $this->catr->findAll();
        $this->botman->hears('category_{{ choice }}', function (BotMan $bot, string $choice) {
            $bot->reply($choice);
        });
        /*foreach ($categories as $category) {
            $this->botman->hears(strtolower(str_replace(' ', '', $category->getTitle())), function (BotMan $botMan) use ($category, $provider){
                foreach ($provider->handle($category) as $tutorial){
                    $botMan->reply(OutgoingMessage::create(sprintf("
                    Catégorie du tutoriel : %s \n
                    Titre du tutoriel : %s \n
                    Description du tutoriel : %s \n
                    Lien youtube du tutoriel : %s \n\n
                    Power By ONASS & ARICA STUDIO
                ", $category->getTitle(), $tutorial->getTitle(), $tutorial->getDescription(), $tutorial->getUrl())));
                }
            });
        }*/

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
                    ->payload('category_'.$category->getId())
            );
        }

        return
            $btnTemplate;
    }
}
