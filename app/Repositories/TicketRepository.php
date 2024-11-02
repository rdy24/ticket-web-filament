<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    public function getPopularTickets($limit = 4)
    {
        return Ticket::orderBy('is_popular', true)->limit($limit)->get();
    }

    public function getAllNewTickets()
    {
        return Ticket::latest()->get();
    }

    public function find($id)
    {
        return Ticket::find($id);
    }

    public function getPrice($ticketId)
    {
        $ticket = Ticket::find($ticketId);

        return $ticket ? $ticket->price : 0;
    }
}