<?php
// src/MessageHandler/CreateUserMessageHandler.php

namespace App\MessageHandler;

use App\Manager\UserManager;
use App\Message\CreateUserMessage;
use App\Entity\User;
use App\DTO\ManageUserDTO; // Предполагаем, что у вас есть DTO для управления пользователями
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserMessageHandler
{

    public function __construct(private readonly UserManager $userManager)
    {
    }

    public function __invoke(CreateUserMessage $message)
    {
        $userDTO = new ManageUserDTO();
        $userDTO->login = $message->getLogin();
        $userDTO->password = $message->getPassword();
        $userDTO->roles = [];
        $user = new User();
        $this->userManager->saveUserFromDTO($user, $userDTO);
    }
}