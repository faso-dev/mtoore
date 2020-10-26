<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Service\Botman;


use App\Entity\Category;
use App\Entity\Tutorial;
use App\Service\Botman\Provider\TutorialProvider;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class TutorialBotService
 * @package App\Service\Botman
 */
class TutorialBotService
{

    public static function handle(TutorialProvider $provider, BotMan $bot, ?Category $category = null, ?int $page = 1)
    {
        if (null !== $category) {
            $paginationTutorials = $provider->handle($category, $page);
            if ($paginationTutorials->getTotalItemCount()){
                /** @var Tutorial[] $tutorials */
                $tutorials = $paginationTutorials->getItems();
                TutorialBotService::replyWithData($bot, $tutorials);
                TutorialBotService::sendPagination($bot, $paginationTutorials, $category);
            }
            else
                TutorialBotService::replyWhenNoDataFound($bot);
        } else
            TutorialBotService::replyWhenInvalidRequestSend($bot);
    }

    /**
     * @param BotMan $bot
     * @param Tutorial[] $tutorials
     */
    public static function replyWithData(BotMan $bot, array $tutorials)
    {
        $elements = [];
        foreach ($tutorials as $tutorial) {
            $elements[] = Element::create($tutorial->getTitle())
                ->subtitle($tutorial->getDescription())
                ->image($tutorial->getThumbnail())
                ->addButton(ElementButton::create('Play on youtube')
                    ->url($tutorial->getUrl())
                );
        }
        $bot->reply(
            GenericTemplate::create()
                ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
                ->addElements($elements)
        );
    }

    /**
     * @param BotMan $bot
     */
    public static function replyWhenNoDataFound(BotMan $bot)
    {
        $bot->reply('We did not find any tutorials for this category');
    }

    /**
     * @param BotMan $bot
     */
    public static function replyWhenInvalidRequestSend(BotMan $bot)
    {
        $bot->reply("I'm not sure I understand what you are asking me, please use the menu below. 😉️😉️😉️");
    }

    private static function sendPagination(BotMan $bot, PaginationInterface $paginationTutorials, Category $item)
    {
        $curentPage = $paginationTutorials->getCurrentPageNumber();
        $itemsPerPage = $paginationTutorials->getItemNumberPerPage();
        $max = ceil($paginationTutorials->getTotalItemCount() / $itemsPerPage);
        $next = $max >= ($curentPage + 1) ? $curentPage + 1 : false;
        $prev = ($curentPage - 1) < 1 ? false : $curentPage - 1;
        $btnTemplate = ButtonTemplate::create('Pagination');
        if ($next) {
            $btnTemplate
                ->addButton(ElementButton::create('Suivant')
                ->type('postback')
                ->payload('pagination_'.$item->getId().'_'.$next));
        }
        if ($prev) {
            $btnTemplate
                ->addButton(ElementButton::create('Précedant')
                ->type('postback')
                ->payload('pagination_'.$item->getId().'_'.$prev));
        }
        $bot->reply($btnTemplate);
    }
}