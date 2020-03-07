<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\UserService;
use App\Repository\OrderRepository;
use App\Repository\PizzaRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 * @package App\Controller
 */
class OrderController extends ApiController
{
    private $orderService;
    private $orderRepository;
    private $pizzaRepository;
    private $entityManager;
    private $orderEntity;

    public function __construct(
        UserService $orderService,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        PizzaRepository $pizzaRepository
    )
    {
        $this->orderEntity = new Order();
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->pizzaRepository = $pizzaRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Get orders
     *
     * @param string $email
     *
     * @return JsonResponse
     */
    public function getOrders(string $email): JsonResponse
    {
        return $this->response(
            $this->orderService->findOrderByEmail($email, $this->orderRepository)
        );
    }

    /**
     * Add Order
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addOrder(Request $request)
    {
        try{
            $request = $this->transformJsonBody($request);
            if (
                !$request ||
                !$request->get('email') ||
                !$request->request->get('contact') ||
                !$request->get('address') ||
                !$request->request->get('pincode') ||
                !$request->get('order_details')
            ){
                throw new \Exception();
            }

            $result = $this->orderService->insertOrder(
                $request,
                $this->entityManager,
                $this->pizzaRepository
            );

            if ("success" === $result['status']) {
                return $this->respondWithSuccess($result['message']);
            }

            return $this->respondWithErrors($result['message']);
        }catch (\Exception $e){
            return $this->respondValidationError("Invalid data passed");
        }
    }
}
