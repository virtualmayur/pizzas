<?php

namespace App\Services;

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
}