@component('mail::message')
# Dear {{ $buyer }}
感謝您的購買！
以下為訂單相關資訊：

- 訂單: {{$order}}
- 商品: {{$item_name}}
- 商品描述: {{$item_description}}
- 數量: {{$quantity}}
- 單價: {{$unit_price}}
- 總額: {{$total_amount}}

## 請您於有效期限內完成付款，期限為： <span style="color: red">{{$expiry_time}}</span>

<hr>

<br>
## 如果您有任何問題，歡迎隨時與我們聯絡！

@component('mail::button', ['url' => 'https://tn710617.github.io/'])
聯絡我們
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
