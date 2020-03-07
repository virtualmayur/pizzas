<?php

namespace App\Services;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class User
 * @package App\Service
 */
class UserService
{
    /**
     * Insert User
     *
     * @param array $params
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return array
     * @throws \Exception
     */
    public function insertUser(
        array $params,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder
    )
    {
        if ($userRepository->findOneBy(['email' => $params['email']]) instanceof User) {
            return [
                "status" => "Exist",
                "message" => "A user with this email already exist"
            ];
        }

        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $params['password']));
        $user->setEmail($params['email']);
        $user->setName($params['name']);
        $user->setCreatedAt(new \DateTime('now'));
        $entityManager->persist($user);
        $entityManager->flush();


        if ($user->getId()) {
            $result = [
                "status" => "success",
                "message" => "User created successfully!"
            ];
        } else {
            $result = [
                "status" => "failed",
                "message" => "Failed to create user"
            ];
        }

        return $result;
    }
}
