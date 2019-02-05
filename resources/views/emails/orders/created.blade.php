@component('mail::message')
# Dear {{ $buyer }}
Thanks for your patronage!

- Order: {{$order}}
- Item: {{$item_name}}
- Item description: {{$item_description}}
- Quantity: {{$quantity}}
- Unit price: {{$unit_price}}
- Amount: {{$total_amount}}

## Kindly make this payment before <span style="color: red">{{$expiry_time}}</span>

<hr>

<br>
## If you have any question, feel free to contact us

@component('mail::button', ['url' => 'https://tn710617.github.io/'])
Contact Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
