<?php

namespace App\Services;

use App\Repository\PizzaRepository;

/**
 * Class Order
 * @package App\Service
 */
class PizzaService
{
    /**
     * @param PizzaRepository $pizzaRepository
     * @return array
     */
    public function fetchAll($pizzaRepository): array
    {
        $pizzas = $pizzaRepository->findAll();

        $pizzaData = [];
        foreach ($pizzas as $pizza) {
            $pizzaData[] = [
                "id" => $pizza->getId(),
                "name" => $pizza->getName(),
                "description" => $pizza->getDescription(),
                "imageUrl" => $pizza->getImageUrl(),
                "price" => $pizza->getPrice(),
                "createdAt" => $pizza->getCreatedAt(),
            ];
        }

        return $pizzaData;
    }
}