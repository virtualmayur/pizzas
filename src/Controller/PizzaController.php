<?php

namespace App\Controller;

use App\Repository\PizzaRepository;
use App\Services\PizzaService;

class PizzaController extends ApiController
{
    private $pizzaService;
    private $pizzaRepository;

    public function __construct(PizzaService $pizzaService, PizzaRepository $pizzaRepository)
    {
        $this->pizzaService = $pizzaService;
        $this->pizzaRepository = $pizzaRepository;
    }

    /**
     * Get all pizzas
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllPizzas()
    {
        return $this->response(
            $this->pizzaService->fetchAll($this->pizzaRepository)
        );
    }
}
