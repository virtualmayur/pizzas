<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends ApiController
{
    private $userRepository;
    private $userService;

    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Registration
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $request = $this->transformJsonBody($request);
        $name = $request->get('name');
        $password = $request->get('password');
        $email = $request->get('email');

        if (empty($name) || empty($password) || empty($email)){
            return $this->respondValidationError("Invalid Username or Password or Email");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $result = $this->userService->insertUser(
            [
                "email" => $email,
                "password" => $password,
                "name" => $name
            ],
            $entityManager,
            $encoder
        );

        if ("Exist" === $result['status']) {
            return $this->respondAlreadyExist($request['message']);
        } elseif ("Failed" === $result['status']) {
            return $this->respondWithErrors($result['message']);
        }

        return $this->respondWithSuccess(sprintf('User %s successfully created', $email));
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
