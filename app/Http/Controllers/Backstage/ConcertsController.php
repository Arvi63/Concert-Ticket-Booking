<?php
namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ConcertsController extends Controller
{
    public function index()
    {
        $concerts = auth()->user()->concerts;

        return view('backstage.concerts.index', compact('concerts'));
    }

    public function create()
    {
        return 'Create New Concert';
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $concert = auth()->user()->concerts()->create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'ticket_price' => request('ticket_price') * 100,
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
        ])->addTickets(request('ticket_quantity'));

        $concert->publish();

        return redirect()->route('concerts.show', $concert);
    }

    public function edit($id)
    {
        $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $concert = auth()->user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', compact('concert'));
    }

    public function update($id)
    {
        $concert = auth()->user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        $update = $concert->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => (int) request('ticket_quantity'),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}
