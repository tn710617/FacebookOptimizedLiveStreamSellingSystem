@component('mail::message')
# Dear {{ $seller->name }}
感謝您使用BuyBuyGo:
<br>
我們收到來自買家 <strong>{{ $buyer }} </strong>的退款申請。
<br>
以下為將退款給買家的訂單金額明細：
<p>
<ul>
    <li>訂單<strong>{{ $orderRelation->order->name }}</strong>共 NTD
        <strong>{{ $orderRelation->order->total_amount}}</strong> 元
    </li>
    <li>賣場：<strong>{{ $orderRelation->order->channel->channel_description }}</strong></li>
</ul>
</p>

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


