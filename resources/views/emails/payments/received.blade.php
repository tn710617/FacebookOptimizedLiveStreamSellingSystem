@component('mail::message')
# Dear {{ $buyer }}
感謝您的惠顧！
<br>
尊貴的客戶，我們已收到您的付款，共 新台幣 <strong> {{ $total_amount }} </strong>元
<br>
您所支付的訂單明細如下：
@foreach ($orderRelations as $orderRelation)
    - 訂單<strong>{{ $orderRelation->order->name }}</strong>: NTD
    <strong>{{ $orderRelation->order->total_amount}}</strong>
@endforeach

您可以登入我們的<a href="{{env('SERVICE_WEBSITE')}}">服務</a>來獲得更多的資訊.
<hr>

<br>
## 如果你有任何問題，隨時歡迎聯絡我們！

@component('mail::button', ['url' => 'https://tn710617.github.io/'])
    聯絡我們
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

