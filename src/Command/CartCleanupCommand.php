<?php

namespace App\Command;

use App\Repository\CartRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CartCleanupCommand extends Command
{
    protected static $defaultName = 'app:cart-cleanup';
    protected static $defaultDescription = 'Remove carts without products';

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var CartRepository */
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
    }


    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('product_limit', InputArgument::OPTIONAL, 'Cart product limit', 0)
            ->addOption('list_id', 'l', InputOption::VALUE_NONE, 'List deleted cart ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $productLimit = $input->getArgument('product_limit');
        $io->note('Your product limit is: '.$productLimit);

        $carts = $this->cartRepository->findAll();

        foreach ($carts as $cart) {
            try {
                if(CartService::countCartProducts($cart) <= $productLimit)
                {
                    $this->entityManager->remove($cart);
                    if($input->getOption('list_id'))
                    {
                        $io->note('Cart with ID '.$cart->getId().' has been deleted successfully');
                    }
                    $this->entityManager->flush();
                }
            } catch (\Exception $ex) {
                $io->error($ex->getMessage().' on cart with ID '.$cart->getId());
            }

        }

        return 0;
    }
}
