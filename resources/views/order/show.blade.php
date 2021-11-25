Confirmation Number :: <a href="{{ route('orders.show',$order->confirmation_number) }}"> {{ $order->confirmation_number }} </a>
Order Total :: ${{ number_format($order->amount/100,2) }}
Billed to Card :: **** **** **** {{ $order->card_last_four }}

@foreach($order->tickets as $ticket)
    Ticket Code :: {{ $ticket->code }}

    <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}">
        Concert Date :: {{ $ticket->concert->date->format('F j, Y') }}

    </time>
    Doors at :: {{ $ticket->concert->date->format('g:ia') }}
@endforeach
