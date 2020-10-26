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
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\OpenGraphElement;
use BotMan\Drivers\Facebook\Extensions\OpenGraphTemplate;
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
            'Salut|Coucou|cc|Bonjour|Slt|Bonsoir|GET_STARTED',
            function (BotMan $bot) {
                $bot->reply(
                    $this->welcome()
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

                       /* $message = OutgoingMessage::create(sprintf("
                                Category : %s\n
                                Title : %s\n
                                Description : %s \n
                                ", $category->getTitle(),
                                    $tutorial->getTitle(),
                                    $tutorial->getDescription()));

                        $bot->reply($message);*/
                        $bot->reply(
                            OpenGraphTemplate::create()
                                ->addElement(OpenGraphElement::create()->url('https://www.youtube.com/watch?v=IXt77zHffMw'))
                                ->addElements([
                                    OpenGraphElement::create()->url($tutorial->getUrl())
                                ])
                        );
                    }
                }else{
                    $bot->reply(sprintf("We did not find any tutorials for this category"));
                }

            }else{
                $bot->reply(sprintf("INVALID REQUEST (:"));
            }


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
                    ->payload('category_'.$category->getId())
            );
        }

        return $btnTemplate;
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
        }else{
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour');
        }

        return $this->redirectToRoute('dashboard');
    }
}
