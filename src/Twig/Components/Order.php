<?php

namespace App\Twig\Components;

// use App\Entity\Order as EntityOrder;
use App\Form\OrderType;
use App\Repository\CityRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class Order extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?int $selectedCity = 0;

    public function __construct(private CityRepository $cityRepository, private ProductRepository $productRepository) {}

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(OrderType::class);
    }

    public function getTotalPrice(SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        $data = [];

        foreach ($cart as $id => $quantity) {
            $data[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $data));
    }

    public function getShippingCost()
    {
        if ($this->selectedCity) {
            return $this->cityRepository->find($this->selectedCity);
        }
    }
}
