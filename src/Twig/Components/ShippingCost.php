<?php

namespace App\Twig\Components;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ShippingCost
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?City $city = null;

    public string $name = '';

    public function __construct(EntityManagerInterface $entityManager) {}

    public function getShipppingCost()
    {
        return rand(0, 1000);
    }
}
