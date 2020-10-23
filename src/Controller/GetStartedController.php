<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Controller;

use App\Entity\GetStarted;
use App\Form\GetStartedType;
use App\Repository\GetStartedRepository;
use App\Service\Botman\FacebookBotmanService;
use BotMan\Drivers\Facebook\Providers\FacebookServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class GetStartedController
 * @package App\Controller
 * @Route("/dashboard")
 */
class GetStartedController extends AbstractController
{
    /**
     * @var string
     */
    private $MESSENGER_URL;

    public function __construct(FacebookBotmanService $provider)
    {
        $this->MESSENGER_URL = 'https://graph.facebook.com/v3.0/me/messenger_profile?access_token='.$provider->getFacebookToken();
    }

    /**
     * @Route("/get/started", name="get_started")
     * @param HttpClientInterface $http
     * @param Request $request
     * @param GetStartedRepository $getStartedRepository
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function index(HttpClientInterface $http, Request $request, GetStartedRepository $getStartedRepository, EntityManagerInterface $manager)
    {
        $getstarted = $getStartedRepository->findAll();

        /** @var GetStarted $getstarted */
        $getstarted = $getstarted[0] ?? new GetStarted();

        $form = $this->createForm(GetStartedType::class, $getstarted);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if (!$getstarted->getId()){
                $manager->persist($getstarted);
            }
            $manager->flush();

            $response = $http->request(Request::METHOD_POST, $this->MESSENGER_URL, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    "greeting" => [
                        [
                            "locale" => "default",
                            "text" => $getstarted->getWelcome()
                        ]
                    ]
                ])
            ]);

            if ($response->getStatusCode() === Response::HTTP_OK){
                $this->addFlash('succes', 'Greeting text was set');
            }else {
                $this->addFlash('error', 'Something went wrong: ');
            }

        }

        return $this->render('get_started/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
