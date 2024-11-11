<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    protected $frontService;

    public function __construct(FrontService $frontService)
    {
        $this->frontService = $frontService;
    }

    public function index()
    {
        $data = $this->frontService->getFrontPageData();
        return view('front.index', $data);
    }

    public function details(Ticket $ticket)
    {
        return view('front.details', [
            'ticket' => $ticket
        ]);
    }

    public function category(Category $category)
    {
        // $categories = $this->frontService->getAllCategories($category);
        return view('front.category', [
            'category' => $category
        ]);
    }
}
