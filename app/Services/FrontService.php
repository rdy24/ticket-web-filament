<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\SellerRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;

class FrontService 
{
    protected $categoryRepository;
    protected $ticketRepository;
    protected $sellerRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, TicketRepositoryInterface $ticketRepository, SellerRepositoryInterface $sellerRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->ticketRepository = $ticketRepository;
        $this->sellerRepository = $sellerRepository;
    }

    public function getFrontPageData()
    {
        $categories = $this->categoryRepository->getAllCategories();
        $popularTickets = $this->ticketRepository->getPopularTickets(4);
        $newTickets = $this->ticketRepository->getAllNewTickets();
        $sellers = $this->sellerRepository->getAllSellers();

        return [
            'categories' => $categories,
            'popularTickets' => $popularTickets,
            'newTickets' => $newTickets,
            'sellers' => $sellers
        ];
    }
}