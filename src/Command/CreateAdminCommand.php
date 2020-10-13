<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CreateAdminCommand
 * @package App\Command
 */
class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, string $name = null)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        parent::__construct($name);

    }

    protected function configure()
    {
        $this
            ->setDescription('Cette commande permet de créer un compte administrateur')
            ->addArgument('username', InputArgument::REQUIRED, "Nom d'utilisateur : ")
            ->addArgument('password', InputArgument::REQUIRED, "Mot de passe : ")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $admin = (new User())
            ->setUsername($username)
            ;

        $admin->setPassword($this->encoder->encodePassword($admin, $password));

        $this->manager->persist($admin);
        $this->manager->flush();

        $io->success(sprintf("Le compte administrateur %s a bien été crée avec succès ! \nIdentifiants de connexion \nUSERNAME : %s \nPassword: %s", $username, $username, $password));

        return Command::SUCCESS;
    }
}
