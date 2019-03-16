@component('mail::message')
# Dear {{ $buyer }}
感謝您的惠顧！
<br>
尊貴的客戶，我們已收到您的退款申請!
<br>
您申請的退款明細如下：
    - 訂單<strong>{{ $orderRelation->order->name }}</strong>: NTD
    <strong>{{ $orderRelation->order->total_amount}}</strong>

您可到您使用的第三方支付帳戶中查詢！我們已向第三方支付服務提出申請，待程序完成後，您將會收到你要求的退款款項。若我們尚未跟您請款，此筆款項之後將不會列入我們的請款金額當中。
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


