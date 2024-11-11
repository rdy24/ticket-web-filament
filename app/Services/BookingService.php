<?php

namespace App\Services;

use App\Models\BookingTransaction;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BookingService 
{
    protected $bookingRepository;
    protected $ticketRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository, TicketRepositoryInterface $ticketRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->ticketRepository = $ticketRepository;
    }

    public function calculateTotals($ticketId, $totalParticipant)
    {
        $ppn = 0.11;
        $price = $this->ticketRepository->getPrice($ticketId);

        $subTotal = $price * $totalParticipant;
        $totalPPn = $subTotal * $ppn;
        $totalAmount = $subTotal + $totalPPn;

        return [
            'sub_total' => $subTotal,
            'total_ppn' => $totalPPn,
            'total_amount' => $totalAmount
        ];
    }

    public function storeBookingInSession($ticket, $validatedData, $totals)
    {
        session()->put('booking', [
            'ticket_id' => $ticket->id,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'started_at' => $validatedData['started_at'],
            'total_participant' => $validatedData['total_participant'],
            'sub_total' => $totals['sub_total'],
            'total_ppn' => $totals['total_ppn'],
            'total_amount' => $totals['total_amount']
        ]);
    }

    public function payment()
    {
        $booking = session()->get('booking');

        $ticket = $this->ticketRepository->find($booking['ticket_id']);

        return [
            'ticket' => $ticket,
            'booking' => $booking
        ];
    }

    public function paymentStore(array $validated)
    {
        $booking = session('booking');
        $bookingTransactionId = null;

        DB::transaction(function () use ($validated, $booking, &$bookingTransactionId) {
            if(isset($validated['proof'])) {
                $proofPath = $validated['proof']->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['name'] = $booking['name'];
            $validated['email'] = $booking['email'];
            $validated['phone_number'] = $booking['phone_number'];
            $validated['total_participant'] = $booking['total_participant'];
            $validated['started_at'] = $booking['started_at'];
            $validated['total_amount'] = $booking['total_amount'];
            $validated['ticket_id'] = $booking['ticket_id'];
            $validated['is_paid'] = false;
            $validated['is_paid'] = BookingTransaction::generateUniqueTrxId();

            $bookingTransaction = $this->bookingRepository->createBooking($validated);

            $bookingTransactionId = $bookingTransaction->id;

        });

        return $bookingTransactionId;
    }
}