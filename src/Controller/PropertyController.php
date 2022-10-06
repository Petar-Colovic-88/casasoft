<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PropertyRepository;

class PropertyController extends AbstractController
{
    public function __construct(private PropertyRepository $propertyRepository) {}

    #[Route('/property/managers', name: 'property.manager', methods: 'GET')]
    public function managers(): JsonResponse
    {
        return $this->json($this->propertyRepository->findByManagers());
    }

    #[Route('/property/admins', name: 'property.admin', methods: 'GET')]
    public function admins(): JsonResponse
    {
        return $this->json($this->propertyRepository->findByAdmins());
    }
}
