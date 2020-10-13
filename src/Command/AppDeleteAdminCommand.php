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
 * Class AppDeleteAdminCommand
 * @package App\Command
 */
class AppDeleteAdminCommand extends Command
{
    protected static $defaultName = 'app:delete-admin';

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
            ->setDescription('Cette commande permet de supprimer un compte administrateur')
            ->addArgument('username', InputArgument::REQUIRED, "Nom d'utilisateur")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        $manager = $this->manager->getRepository(User::class);

        $admin = $manager->findOneBy(['username' => $username]);

        if ($admin){
            $this->manager->remove($admin);
            $this->manager->flush();
            $io->success(sprintf("Le compte administrateur %s a bien été supprimé avec succès", $username));

        }else{
            $io->error(sprintf("Compte %s introuvable", $username));
        }
        return Command::SUCCESS;
    }
}
