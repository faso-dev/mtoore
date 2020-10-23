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

        $this->botman->hears('category_{choice}', function (BotMan $bot, string $choice) use ($provider){
            /** @var Category $category */
            $category = $this->catr->find((int)$choice);

            if (null !== $category){
                $tutorials = $provider->handle($category);
                if ($tutorials){
                    foreach ($tutorials as $tutorial){
                        $bot->reply(sprintf("
                    Category : %s\nTitle : %s\nDescription : %s \nShow tutoriel : %s\n
                ", $category->getTitle(), $tutorial->getTitle(), $tutorial->getDescription(), $tutorial->getUrl()));
                    }
                    $bot->reply(
                        $this->buildConversationButtons()
                    );
                }else{
                    $bot->reply(sprintf("Nous n'avons trouvez aucun tutoriel pour cette catégorie"));
                }

            }else{
                $bot->reply(sprintf("Demande incorrecte (:"));
            }


        });

        $this->botman->listen();

        return new Response();
    }

    private function buildConversationButtons()
    {

        $categories = $this->catr->findAll();

        $btnTemplate = ButtonTemplate::create("Bienvenue sur MTOORE, vote bot pour apprendre la réalité augmentée");

        /*foreach ($categories as $category) {
            $btnTemplate->addButton(
                ElementButton::create($category->getTitle())
                    ->type('postback')
                    ->payload('category_'.$category->getId())
            );
        }*/

        return $btnTemplate->addButton(ElementButton::create('Documentation')
            ->url('https://www.youtube.com/embed/k5JAL8qIzr0')
        );
    }
}
