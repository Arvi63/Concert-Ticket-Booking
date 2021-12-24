
<h1>{{ $concert->title }}</h1>

<h3>Total Tickets Remaining : {{ $concert->ticketsRemaining() }} </h3>

<h3> Total Tickets Sold : {{ $concert->ticketsSold() }} </h3>

<h3>This is show is {{ $concert->percentSoldOut() }}</h3>
<progress value="{{ $concert->ticketsSold() }}" max="{{ $concert->totalTickets() }}"></progress>

<h3>Total Revenue : ${{ $concert->revenueInDollars() }} </h3>