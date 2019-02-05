@component('mail::message')
# Dear {{ $buyer }}
Thanks for your patronage!
<br>
We've received your payment NTD <strong> {{ $total_amount }}.</strong>
<br>
Kindly refer to the paid orders in detail as follows:
@foreach ($orderRelations as $orderRelation)
    - Order <strong>{{ $orderRelation->order->name }}</strong>: NTD <strong>{{ $orderRelation->order->total_amount}}</strong>
@endforeach

For more information, you could login BuyBuyGo service <a href="#">here</a>.
<hr>

<br>
## If you have any questions, feel free to contact us

@component('mail::button', ['url' => 'https://tn710617.github.io/'])
    Contact Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

