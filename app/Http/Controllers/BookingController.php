<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\BookingTransaction;
use App\Models\Ticket;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function booking(Ticket $ticket)
    {
        return view('front.booking', [
            'ticket' => $ticket
        ]);
    }

    public function bookingStore(Ticket $ticket, StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $totals = $this->bookingService->calculateTotals($ticket->id, $validated['total_participant']);
        $this->bookingService->storeBookingInSession($ticket, $validated, $totals);

        return redirect()->route('front.payment');
    }

    public function payment()
    {
        $data = $this->bookingService->payment();
        return view('front.payment', $data);
    }

    public function paymentStore(StorePaymentRequest $request)
    {
        $validated = $request->validated();
        $bookingTransactionId = $this->bookingService->paymentStore($validated);

        if($bookingTransactionId) {
            return redirect()->route('front.booking_finished', $bookingTransactionId);
        }

        return redirect()->route('front.index')->withErrors(['error' => 'Payment failed. Please try again']);
    }

    public function bookingFinished(BookingTransaction $bookingTransaction)
    {
        return view('front.booking_finished', [
            'bookingTransaction' => $bookingTransaction
        ]);
    }
    public function checkBooking()
    {
        return view('front.check_booking');
    }

    public function checkBookingDetails(CheckBookingRequest $request)
    {
        $data = $request->validated();

        $booking = $this->bookingService->checkBookingDetails($data);

        return redirect()->route('front.booking_detail', $booking->id);
    }

    public function bookingDetail(BookingTransaction $bookingTransaction)
    {
        return view('front.booking_detail', [
            'bookingTransaction' => $bookingTransaction
        ]);
    }
}
