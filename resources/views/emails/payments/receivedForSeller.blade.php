@component('mail::message')
# Dear {{ $seller->name }}
感謝您使用BuyBuyGo:
<br>
我們已收到來自買家 <strong>{{ $buyer }} </strong>的款項，共新台幣 <strong> {{ $paymentServiceOrder->total_amount }} </strong> 元。
<br>
以下為此次被支付的訂單及賣場明細：
@foreach ($orderRelations as $orderRelation)
<p>
<ul>
    <li>訂單<strong>{{ $orderRelation->order->name }}</strong>共 NTD
        <strong>{{ $orderRelation->order->total_amount}}</strong> 元</li>
    <li>賣場：<strong>{{ $orderRelation->order->channel->channel_description }}</strong></li>
</ul>
</p>
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


