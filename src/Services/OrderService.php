<?php

namespace App\Services;

use App\Entity\{Order, OrderDetail};
use App\Repository\{OrderRepository, PizzaRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Order
 * @package App\Service
 */
class OrderService
{
    /**
     * @param $email
     * @param OrderRepository $orderRepository
     * @return array
     */
    public function findOrderByEmail($email, $orderRepository): array
    {
        $orders = $orderRepository->findBy(["email" => $email]);

        $orderData = [];
        foreach ($orders as $order) {
            $pizzaDetails = [];
            foreach ($order->getOrderDetails() as $detail) {
                $pizzaDetails[] = [
                    "pizza_name" => $detail->getPizza()->getName(),
                    "pizza_description" => $detail->getPizza()->getDescription(),
                    "pizza_image" => $detail->getPizza()->getImageUrl(),
                    "quantity" => $detail->getQuantity(),
                    "price" => $detail->getPrice(),
                ];
            }
            $orderData[] = [
                "id" => $order->getId(),
                "email" => $order->getEmail(),
                "contact" => $order->getContact(),
                "address" => $order->getAddress(),
                "pincode" => $order->getPincode(),
                "created_at" => $order->getCreatedAt(),
                "order_details" => $pizzaDetails
            ];
        }

        return $orderData;
    }

    /**
     * Insert Order
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PizzaRepository $pizzaRepository
     *
     * @return string
     * @throws \Exception
     */
    public function insertOrder(
        Request $request,
        EntityManagerInterface $entityManager,
        PizzaRepository $pizzaRepository
    )
    {
        $order = new Order();
        $order->setEmail($request->get('email'))
            ->setContact($request->get('contact'))
            ->setAddress($request->get('address'))
            ->setPincode($request->get('pincode'))
            ->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($order);
        $entityManager->flush();
        $orderDetails = $request->get('order_details');

        foreach ($orderDetails as $orderDetail) {
            $pizzaDetails = $pizzaRepository->find($orderDetail['pizza']);
            $orderData = new OrderDetail();
            $orderData->setOrders($order)
                ->setPizza($pizzaDetails)
                ->setQuantity($orderDetail['quantity'])
                ->setPrice($pizzaDetails->getPrice() * $orderDetail['quantity']);
            $entityManager->persist($orderData);
            $entityManager->flush();
        }

        if ($order->getId()) {
            $result = [
                "status" => "success",
                "message" => "Order created successfully!"
            ];
        } else {
            $result = [
                "status" => "failed",
                "message" => "Failed to create order"
            ];
        }

        return $result;
    }
}
