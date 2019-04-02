[English](./README.md) | 繁體中文

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

<h1 align="center">BuyBuyGo-Facebook直播拍賣優化系統</h1>

## 前言
這是一個，齊集我的戰友們的心血以及努力所完成的一個 side project !
參與這個 project 的，有 iOS 端， Android 端，以及 Web 端，當然，還有我的 backend 。
此 side project 旨在解決，傳統 Facebook 在直播購物時的不方便的地方，例如：
- 買家購買商品時，無法選取數量
- 賣家結束直播後，需要經由訊息的訊息記錄當作為交易的憑依，當訊息量大時，對賣家來說十分費神
- 賣家無法系統化的管理商品訂單，以及商品的銷售歷史紀錄
- 傳統付款幾乎都還停留在轉帳交易，對買賣雙方來說都不夠方便

上面只是大略的列出一些我們想要解決的問題，我會在下面比較細節性的列出我們所解決的問題

## 我們解決的問題
### 買家
- 清楚的商品資訊： 我們的系統，提供了買家下面的資訊，就算買家的耳朵不好，也可以清楚收到商品資訊。
    - 商品圖片
    - 商品敘述
    - 商品剩餘數量
- 我們優化了購買的介面: 從此之後，買家不需要在小小的螢幕上輸入+1+1，買家只需要選擇購買數量，按下確認鍵，我們的系統會立即為買家成立訂單。 
- 可追朔的歷史: 我們的訂單系統提供買家三個月前，甚至去年購買的商品明細，可以輕易查詢曾經購買過的品項。
- Email即時通知: 在以下三種狀況，我們都提供了即時的Email通知，提供完美的使用者體驗
    - 新訂單成立
    - 買家成功付款成功
    - 買家申請退款成功
- 輕鬆付款： 不管是國內還是國外的買家，都可以輕鬆地利用我們提供的第三方支付來完成付款，我們的服務支援以下兩家第三方支付
    - 國內: 歐付寶
    - 國際: PayPal
- 輕鬆退款： 我們的服務允許了退款機制，只要買賣雙方同意，退款只需要一個按鈕。

### 賣家
- 即時商品資訊: 在直播的過程中，賣家即時掌握已銷售數量以及現有庫存。
- 收款功能： 我們的系統提供了以下兩家金流服務，分為國內以及國際 
    - 國內: 歐付寶
    - 國際: PayPal
- 退款功能： 提供賣家設定，在一定天數內允許買方退款。
- 訂單系統： 不需在經由訊息視窗統整交易資訊，使用訂單系統來記錄一切的細節
- 銷售紀錄： 提供更進一步的銷貨紀錄，包含以下資訊：
    - 成本
    - 單價
    - 售出數量
    - 利潤
    - 營業額
    
## 我的戰友們
### iOS
- [Albert](https://github.com/asdfg51014/FacebookLiveStreamingShopingApp)
- [Chelsea](https://github.com/chelsealin88/streamliveproject)
- [Jerry](https://github.com/aa08666/Livestream-shopping_iOS)
     
### Android
- [Lester](https://github.com/jhengjhe/BuyBuyGo)
- [James](https://github.com/tn710617/FacebookOptimizedLiveStreamSellingSystem)
     
### Web
- [Kai](https://github.com/LiaoYingKai/comeBuy)
- [Askie](https://github.com/askiebaby/buy-everything)

## 運行流程
這邊我將會簡單的描述一下這個專案的流程
1. <span style="color:red">我們的賣家開了一場Facebook直播，並取得直播網址</span>
2. <span style="color:red">賣家登入我們的服務，新增等等要賣的商品資料</span>
3. <span style="color:red">賣家在我們的服務內，輸入等等直播的網址，還有針對這場直播的一些敘述。然後我們的服務會提供給賣家一個頻道的識別碼</span>
4. <span style="color:red">若此時因不可抗因素，賣家與服務斷開，當賣家再次與我們的服務連接時，我們的服務會記得上一次的狀態，並且詢問賣家是否要繼續上一次的狀態，或者重新開始</span>
5. <span style="color:blue">這時候，買家經由賣家FB的聊天視窗，取得我們服務的頻道識別碼</span>
6. <span style="color:blue">買家輸入剛剛拿到的頻道識別碼，然後加入頻道，這時候因為賣家還沒有推播任何商品，所以畫面上是顯示尚未有商品在推播中</span>
7. <span style="color:blue">賣家推播第一個商品，商品推播之後，我們可以在畫面上看到商品的圖片，名稱，以及剩餘的數量，還有已賣數量</span>
8. <span style="color:red">買家可以自由地選擇想要的數量，並且下單！買家下單之後，商品目前的剩餘數量會再買賣雙方即時更新！</span>
9. <span style="color:blue">買家下單之後，會即時的收到Email確認信</span>
10. <span style="color:blue">當賣家結束直播之後，買家畫面會即時顯示直播已關閉</span>
11. <span style="color:blue">無痛付款！我們支援了兩家金流付款！在台灣，我們使用AllPay付款，AllPay裡面又支援了各種不同的方式可以付款</span>
12. <span style="color:blue">如果是來自國外的買家，我們支援使用PayPal付款</span>
13. <span style="color:blue">付款時，買家可以新增收件人資料。一個買家最多可以同時擁有五個收件人地址</span>
14. <span style="color:blue">買家付款之後，不需要忐忑的等待！我們即時的給你Email通知，讓您知道我們已經收到你的付款了</span>
15. <span style="color:red">那賣家呢？到底什麼時候該出貨？ 我們也幫你想好了！ 一旦有訂單被結帳，我們馬上通知！只要你喜歡，連你的另外一半我們都可以通知!</span>
16. <span style="color:blue">買家付完錢了，我們來看看訂單狀態吧！已完成付款的會顯示已付款，未付款的當然就是未付款！未付款的訂單，三天後會自動失效，並且會顯示已經失效。失效之後，再過三天會被刪除</span>
17. <span style="color:blue">若是買家付款之後，覺得不喜歡或是有其他因素，只要買賣雙方同意，我們的系統也支援退款功能。</span>
18. <span style="color:red">賣家可以確認訂單狀態，已付跟未付，一目瞭然！</span>
19. <span style="color:red">如果賣家想知道銷售的狀況，我們提供了成功銷售的各項資料，包含成本、單價、售出數量、利潤、營業額</span>
20. <span style="color:red">在我們的系統中，你可以是個買家，你也可以是一個賣家！</span>

## 安裝流程
- 下載此專案
```bash
git clone https://github.com/tn710617/FacebookOptimizedLiveStreamSellingSystem.git
```

- 在專案底下輸入
```bash
composer install
```

- 建立你自己的資料庫

- 建立`.env`檔
```bash
cp .env.example .env
```

- 建立key
```
php artisan key:generate
```

- 建立表格
```
php artisan migrate
```

- 設定`.env`檔，設定如下：
```bash
vim .env
```
- 下面為詳細的`.env`設定細節
1. AWS SES
```
MAIL_DRIVER=ses
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=yourMailAddress
MAIL_FROM_NAME=whateverNameYouLike
SES_KEY=yourSESKey
SES_SECRET=yourSESSecret
SES_REGION=yourSESRegion
```

2. FACEBOOK
```
FACEBOOK_API_APP_ID=yourID
FACEBOOK_API_APP_SECRET=yourSecret
FACEBOOK_API_DEFAULT_GRAPH_VERSION=theGraphAPIVersionYouUse
FBEndpoint=me?fields=id,name,email
```

3. AllPay
```
ALLPAY_RETURN_URL=theURLOfReceivingNotificationFromAllPayAfterThePaymentIsCleared
HASHKEY=yourKey
HASHIV=yourIV
MERCHANTID=yourMerchantID
```

4. PayPal
```
# PayPal Setting & API Credentials - sandbox - common
## If enable sandbox
PAYPAL_SANDBOX_ENABLESANDBOX=true

PAYPAL_SANDBOX_API_USERNAME=BuyBuyBuyGoGo_api1.gmail.com
PAYPAL_SANDBOX_API_PASSWORD=
PAYPAL_SANDBOX_API_ClientID=
PAYPAL_SANDBOX_API_SECRET=
PAYPAL_SANDBOX_API_CERTIFICATE=
PAYPAL_SANDBOX_MAIL=buybuybuygogo@gmail.com

# During paying process, if the user cancel the transaction, where would he go to
PAYPAL_SANDBOX_CANCEL_URL=

# PayPal Setting & API Credentials - sandbox - IPN
PAYPAL_SANDBOX_SAVE_LOG_FILE=true
PAYPAL_SANDBOX_SEND_CONFIRMATION_EMAIL=false

## When payment is cleared, an IPN message will send to this URL
PAYPAL_SANDBOX_NOFITY_URL=


#PayPal Setting & API Credentials - sandbox - REST API
## Where you want to do further action such as authorize after the order is approved
PAYPAL_SANDBOX_RETURN_URL=

## The intent of created orders
PAYPAL_SANDBOX_INTENT_OF_CREATED_ORDERS=AUTHORIZE

## LOGIN. When the customer clicks PayPal Checkout, the customer is redirected to a page to log in to PayPal and approve the payment.
## BILLING. When the customer clicks PayPal Checkout, the customer is redirected to a page to enter credit or debit card and other relevant billing information required to complete the purchase.
PAYPAL_SANDBOX_LANDING_PAGE=LOGIN

## GET_FROM_FILE. Use the customer-provided shipping address on the PayPal site.
## NO_SHIPPING. Redact the shipping address from the PayPal site. Recommended for digital goods.
## SET_PROVIDED_ADDRESS. Use the merchant-provided address. The customer cannot change this address on the PayPal site.
PAYPAL_SANDBOX_SHIPPING_PREFERENCES=SET_PROVIDED_ADDRESS

## CONTINUE. After you redirect the customer to the PayPal payment page, a Continue button appears. Use this option when the final amount is not known when the checkout flow is initiated and you want to redirect the customer to the merchant page without processing the payment.
## PAY_NOW. After you redirect the customer to the PayPal payment page, a Pay Now button appears. Use this option when the final amount is known when the checkout is initiated and you want to process the payment immediately when the customer clicks Pay Now.
PAYPAL_SANDBOX_USER_ACTION=PAY_NOW


#PayPal Setting & API Credentials - live
PAYPAL_LIVE_API_USERNAME=
PAYPAL_LIVE_API_PASSWORD=
PAYPAL_LIVE_API_SECRET=
PAYPAL_LIVE_API_CERTIFICATE=
```

5. 訂單選項
```
# How many days you prefer orders to be expired or deleted or completed.
# 訂單到期，刪除，以及完成的天數設定
DAYS_OF_ORDER_TO_BE_EXPIRED=3
DAYS_OF_ORDER_TO_BE_DELETED=6
DAYS_OF_ORDER_TO_BE_COMPLETED=7
```

6. 照片尺寸
```
# 上傳商品時，你希望圖片的尺寸
# The size you prefer of the image uploaded by add-new-item function
ITEM_IMAGE_TO_BE_RESIZED_HEIGHT=416
ITEM_IMAGE_TO_BE_RESIZED_WIDTH=300
```

7. 通知郵件
```
# 通知郵件中，你希望可以將使用者帶過去的連結，例如你的服務首頁
# The link you would like to show on notification mail to let user click and go through.
SERVICE_WEBSITE=
```

## 文件
[API文件在此](https://tn710617.github.io/API_Document/FacebookOptimizedSellingSystem/)

## 感想
這次真的很高興可以跟各端的戰友們一起做這個專案！在過程中我們都遇到了一些技術的難題，也都各自的去突破它們！
在這個專案中，當有人卡關延遲進度時，我們沒有責怪，只有共同討論面對。
對我們來說，我們沒有來自於戰友們的壓力。唯一的壓力來源，只能是自己對自己的要求！
對我來說，這不只是一個專案，更是我人生中一段很有意義的經歷！

![](https://i.imgur.com/9p36cP2.jpg)



