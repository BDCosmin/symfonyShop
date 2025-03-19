<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /** @var Cart */
    private $cart;

    /** @var SessionInterface */
    private $session;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->getCurrentCart();
    }

    public function getCart(): Cart
    {
        if (!$this->cart) {
            $this->getCurrentCart();
        }

        return $this->cart;
    }

    public static function countCartProducts($cart)
    {
        $total = 0;
        foreach ($cart->getCartItems() as $cartItem) {
            $total += $cartItem->getQuantity();
        }
        return $total;
    }

    public function getCartCount()
    {
       return self::countCartProducts($this->cart);
    }

    public function getCartTotal()
    {
        $total = 0;
        foreach ($this->cart->getCartItems() as $cartItem) {
            $total += $this->getCartItemTotal($cartItem);
        }
        return $total;
    }

    public function getCartItemTotal(CartItem $cartItem)
    {
        return intval($cartItem->getQuantity() * $cartItem->getProduct()->calculateDiscount());
    }
    public function getPromoCartTotal()
    {
        return $this->getCartTotal()*0.9;
    }

    public function add(Product $product, $quantity=1)
    {
        $cartItem = $this->getCartItemForProduct($product);
        if($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cartItem->setCart($this->cart);
        }
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    public function update(Product $product, $quantity=1)
    {
        $cartItem = $this->getCartItemForProduct($product);
        if($cartItem) {
            $cartItem->setQuantity($quantity);
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        }
    }

    public function delete(Product $product)
    {
        $cartItem = $this->getCartItemForProduct($product);

        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }
    public function empty()
    {
        foreach ($this->cart->getCartItems() as $cartItem) {
            $this->entityManager->remove($cartItem);
        }
        $this->entityManager->flush();
    }

    private function getCartItemForProduct(Product $product):?CartItem
    {
        foreach ($this->cart->getCartItems() as $cartItem) {
            if($cartItem->getProduct()->getId() == $product->getId()){
                return $cartItem;
            }
        }
        return null;
    }

    private function getCurrentCart()
    {
        if ($this->session->has('cart_id')) {
            $cartId = $this->session->get('cart_id');
            $cart = $this->entityManager->getRepository(Cart::class)->find($cartId);

            if ($cart) {
                $this->cart = $cart;
                return;
            }
        }

        // If no cart found, create a new one
        $this->cart = new Cart();
        $this->entityManager->persist($this->cart);
        $this->entityManager->flush();

        // Set the session value for the cart ID
        $this->session->set('cart_id', $this->cart->getId());
    }
}