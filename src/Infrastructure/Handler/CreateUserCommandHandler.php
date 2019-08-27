<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Account\Infrastructure\Handler;

use Ergonode\Account\Domain\Command\User\CreateUserCommand;
use Ergonode\Account\Domain\Entity\User;
use Ergonode\Account\Domain\Repository\UserRepositoryInterface;
use Ergonode\Account\Infrastructure\Encoder\UserPasswordEncoderInterface;

/**
 */
class CreateUserCommandHandler
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @param UserRepositoryInterface      $repository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->repository = $repository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param CreateUserCommand $command
     *
     * @throws \Exception
     */
    public function __invoke(CreateUserCommand $command)
    {
        $user = new User(
            $command->getId(),
            $command->getFirstName(),
            $command->getLastName(),
            $command->getEmail(),
            $command->getLanguage(),
            $command->getPassword(),
            $command->getRoleId(),
            $command->getAvatarId(),
            $command->isActive()
        );

        $encodedPassword = $this->userPasswordEncoder->encode($user, $command->getPassword());
        $user->changePassword($encodedPassword);

        $this->repository->save($user);
    }
}
