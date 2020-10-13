<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Service\Botman;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Facebook\FacebookDriver;

/**
 * Class FacebookBotmanService
 * @package App\Service\Botman
 */
class FacebookBotmanService
{

    /**
     * @var string
     */
    private $facebookToken;
    /**
     * @var string
     */
    private $facebookVerification;

    /**
     * FacebookBotmanService constructor.
     * @param string $facebookToken
     * @param string $facebookVerification
     */
    public function __construct(string $facebookToken, string $facebookVerification)
    {
        $this->facebookToken = $facebookToken;
        $this->facebookVerification = $facebookVerification;

        DriverManager::loadDriver(FacebookDriver::class);
    }

    /**
     * @return BotMan
     */
    public function create(): BotMan
    {
        return BotManFactory::create([
            'facebook' => [
                'token' => $this->facebookToken,
                'verification' => $this->facebookVerification,
            ]
        ]);

    }
}