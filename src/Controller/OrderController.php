<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\OrderRepository;
use App\Repository\PizzaRepository;
use App\Services\PizzaService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends ApiController
{
    private $orderService;
    private $orderRepository;
    private $pizzaRepository;
    private $entityManager;

    public function __construct(
        PizzaService $orderService,
        OrderRepository $orderRepository,
        PizzaRepository $pizzaRepository,
        EntityManagerInterface $entityManager
    )
    {
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

            $order = new Order();
            $order->setEmail($request->get('email'))
                ->setContact($request->get('contact'))
                ->setAddress($request->get('address'))
                ->setPincode($request->get('pincode'))
                ->setCreatedAt(new \DateTime('new'));
            $entityManager->persist($order);
            $entityManager->flush();
            $orderDetails = $request->get('orderDetails');
            $pizzaDetails = $this->pizzaRepository->find($orderDetails[0]['id']);
            $orderDetail = new OrderDetail();
            foreach ($orderDetail as $orderD)
            $orderDetail->setPizza($pizzaDetails->get)
            $orderDetails =
            $data = [
                'status' => 200,
                'success' => "Post added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }
    }
}
