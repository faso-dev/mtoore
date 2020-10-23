<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http =>//faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Service\Botman;


use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class FacebookPersistentMenuService
 * @package App\Service\Botman
 */
class FacebookPersistentMenuService
{
    /**
     * @var HttpClientInterface
     */
    private $http;
    /**
     * @var string
     */
    private $entrypoint;

    public function __construct(HttpClientInterface $http, FacebookBotmanService $service)
    {
        $this->http = $http;
        $this->entrypoint = 'https://graph.facebook.com/v8.0/me/messenger_profile?access_token='.$service->getFacebookToken();
    }

    /**
     * @param Category[] $items
     * @throws TransportExceptionInterface
     */
    public function persistentMenu(array $items)
    {

        $call_actions = [];

        foreach ($items as $item) {
            $call_actions[] = [
                "type" => "postback",
                "title" => $item->getTitle(),
                "payload" => 'category_'.$item->getId()
            ];
        }
        $response = $this->http->request(Request::METHOD_POST, $this->entrypoint, [
            'json' => [
                'persistent_menu' => [
                    [
                        "locale" => "default",
                        "composer_input_disabled" => true,
                        "call_to_actions" => $call_actions
                    ]
                ]
            ]
        ]);
        
        if ($response->getStatusCode() === Response::HTTP_OK) {
            dd($response->toArray());
            return true;
        }
        dd($response->toArray());
        return false;
    }
}