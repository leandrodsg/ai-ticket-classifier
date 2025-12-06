<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketClassifierService;
use App\Http\Requests\StoreTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Ticket::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::create($request->validated());

        // Classify ticket with AI
        try {
            $classifier = app(TicketClassifierService::class);
            $classification = $classifier->classify($ticket->description);

            // Update ticket with AI classification
            $ticket->update([
                'category' => $classification['category'],
                'sentiment' => $classification['sentiment'],
                'confidence' => $classification['confidence'],
            ]);

            // Log the classification
            $classifier->logClassification(
                $ticket->id,
                $ticket->description,
                $classification,
                $classification['processing_time_ms'] ?? null,
                'success'
            );

        } catch (\Exception $e) {
            // Log error but don't fail ticket creation
            \Log::error('AI classification failed during ticket creation', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
                        ->with('success', 'Ticket created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return redirect()->route('tickets.show', $ticket)
                        ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
                        ->with('success', 'Ticket deleted successfully!');
    }
}
