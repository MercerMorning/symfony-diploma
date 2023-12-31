<?php
# src/GraphQL/Mutation/ShipMutation.php
namespace App\GraphQL\Mutation;

use App\DTO\ManageUserDTO;
use App\Entity\User;
use App\Manager\UserManager;
use App\Message\CreateUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class UserMutation implements MutationInterface, AliasedInterface
{
    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }
//    private $factionRepository;
//
//    public function __construct(FactionRepository $factionRepository) {
//        $this->factionRepository = $factionRepository;
//    }

    public function createUser(array $creds): array
    {
        $creds = $creds[0];

        $this->messageBus->dispatch(new CreateUserMessage($creds['login'], $creds['password']));


//        $userDTO = new ManageUserDTO();
//        $userDTO->login = $creds['login'];
//        $userDTO->password = $creds['password'];
//        $userDTO->roles = [];
//        $user = new User();
//        $this->userManager->saveUserFromDTO($user, $userDTO);

        return [
//            'user' => $user,
            'message' => 'User created successfully',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return [
            'createUser' => 'create_user'
        ];
    }
}