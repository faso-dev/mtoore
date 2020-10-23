<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Service\Botman;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class FacebookBotmanFileUploaderService
 * @package App\Service\Botman
 */
class FacebookBotmanFileUploaderService
{
    /**
     * @var HttpClientInterface
     */
    private $http;
    /**
     * @var string
     */
    private $messenger_url;

    /**
     * FacebookBotmanFileUploaderService constructor.
     * @param HttpClientInterface $http
     * @param FacebookBotmanService $service
     */
    public function __construct(HttpClientInterface $http, FacebookBotmanService $service)
    {
        $this->http = $http;
        $this->messenger_url = 'https://graph.facebook.com/v8.0/me/message_attachments?access_token=' . $service->getFacebookToken();
    }

    /**
     * @param UploadedFile $file
     * @throws TransportExceptionInterface
     */
    public function upload(UploadedFile $file)
    {

        $formFields = [
            'message' => json_encode([
                'attachment' => [
                    'type' => 'image',
                    'payload' => [
                        'is_reusable' => true
                    ]
                ]
            ]),
            'file' => DataPart::fromPath($file->getRealPath(), $file->getClientOriginalName()),
        ];

        $formData = new FormDataPart($formFields);
        $response = $this->http->request(Request::METHOD_POST, $this->messenger_url, [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),

        ]);

        if ($response === Response::HTTP_OK) {
            dd($response->toArray());
        } else {
            dd($response->toArray());
        }
    }
}