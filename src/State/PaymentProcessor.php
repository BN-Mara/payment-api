<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

class PaymentProcessor implements ProcessorInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $ref = uniqid("XREF");
        $data->setReference($ref);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // Handle the state

    }
}
