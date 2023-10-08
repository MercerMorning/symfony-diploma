<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class ManageUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 32)]
        public string $login = '',

        #[Assert\NotBlank]
        #[Assert\Length(max: 32)]
        public string $password = '',

        #[Assert\Type('array')]
        public array $roles = []
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(...[
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
        ]);
    }

    public static function fromRequest(Request $request): self
    {
        /** @var array $roles */
        $roles = $request->request->get('roles') ?? $request->query->get('roles') ?? [];

        return new self(
            login: $request->request->get('login') ?? $request->query->get('login'),
            password: $request->request->get('password') ?? $request->query->get('password'),
            roles: $roles,
        );
    }
}