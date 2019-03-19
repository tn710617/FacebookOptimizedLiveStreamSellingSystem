English | [繁體中文](./README.zh-TW.md)


<h1 align="center">BuyBuyGo - An optimized system for Facebook live-stream video selling</h1>

## Introduction
This is the side project into which my comrades and I put our blood, tears, and sweat into!
In this project, we have iOS, Android, Web, and of course Backend.
This project is aimed to solve some inconveniences of Facebook live-stream selling system, for example:
- When purchasing, they can't choose the number
- After the live-stream finishes, sellers have to calculate and conclude everything according to the message record. If the amount of message is big, it will be quite troublesome for sellers.
- Sellers can't manage the order and goods systemically, including the sales records in detail.
- Traditionally, the payment still depends on transferring, which is not quite convenient for both buyer and seller.

I just roughly listed some problems we would like to solve. I will list all of the problems we've solved in detail as follows:

## The solved problems
### For buyers
- Clear goods information: Our system provides buyers with the following information. It's so friendly for even people with bad hearing. 
    - Photo of goods
    - Description of goods
    - Remaining quantity of goods
- The optimized interface: With our system, buyers don't have to carefully type +1 +1 on the small screen anymore. Just simply choose the number you want, and press confirm button, and then an order will be automatically created.
- Traceable history: Our system provides buyers with information of purchased items, including the one purchased 3 months ago, or even 1 year ago. 
- Instant Email Notification: Our system provide instant email notification in the following cases, providing a perfect user experience.
    - When an order is created
    - When a payment is cleared
    - When an order is refunded
- Easy payment: No matter you are foreigner or local, you could easily make your payment with our third party gateway. Our system supports two payment gateways below:
    - In Taiwan: AllPay
    - International: PayPal
- Easy refund: Our service allow refund request. As long as both buyer and seller agree, what it takes to refund is only a button.

### For sellers
- Real time item information: During live-stream, sellers could get real time information of sold item number and stock
- Payment collection: Our service provides seller with two payment gateways towards domestic and international as follows:
    - Domestic: AllPay
    - International: PayPal
- Refund: Sellers are free to set up how many days a refund request is allowed within.
- Order system: There is no need to filter or organise your transaction via message box. With order system of our services, everything in detail is recorded.
- Sales performance and breakdown: Sales performance and breakdown will be provided including the following information.
    - Cost
    - Unit price
    - Sold quantity
    - Profit
    - Turnover
    
## My comrades
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

## Actual running process
Here I'm going simply go over the whole process of using this project.
1. <span style="color:red">A seller start a live-stream, and get URL of the live-stream</span>
2. <span style="color:red">The seller login our service, and add items he would like to sell later</span>
3. <span style="color:red">The seller start a channel in our service, and provide the URL and some description for this channel, and our service will create a token and provide automatically</span>
4. <span style="color:red">At the moment, if the seller lose the connection from our service due to some unaccountable reason, and when the seller connect to our service again, our service will remember where he were, and ask if he would like to continue from where he were, or go back to home page.</span>
5. <span style="color:blue">And then, via FB's message box, buyers could get the channel token from the seller</span>
6. <span style="color:blue">The buyers join the channel with the channel token they just got, and at the moment there will be no items on stream because the seller hasn't streamed any items yet.</span>
7. <span style="color:blue">The seller streams the first item. After the item is streamed, we will see its photo, name, description, and remaining quantity</span>
8. <span style="color:red">Buyers could freely choose the quantity they want, and place orders. After orders are placed, the remaining quantity will be updated in real time on both buyer and seller side.</span>
9. <span style="color:blue">After the buyer places an order, a notification email will automatically and immediately be sent to the buyer</span>
10. <span style="color:blue">When the seller terminate the channel, a message 'the live-stream has been terminated' will be displayed on buyers' screen </span>
11. <span style="color:blue">Easy payment. Our service supports two payment gateways. In Taiwan, we use AllPay, and there is a variety of payment method in AllPay</span>
12. <span style="color:blue">If you are a foreigner, we also support PayPal payment gateway</span>
13. <span style="color:blue">When making a payment, the buyer could add new recipient. A buyer could have up to 5 recipients</span>
14. <span style="color:blue">After a payment is made, the buyer doesn't have to wait. We will immediately send a notification email to let you know that a payment has been made.</span>
15. <span style="color:red">So what about the seller? When should they ship the goods? Once an order is payed and payment is cleared, we will immediately notify you of this matter. If you prefer, we could also notify your better half.</span>
16. <span style="color:blue">The payment is made, let's check the order status. Those orders that have been paid will display 'paid', and any orders unpaid for three days will be ineffective, and will be automatically deleted after another 3 days</span>
17. <span style="color:blue">After a payment is made, if the buyer doens't feel like it or other reasons. As long as both side agree, our service also support refund feature.</span>
18. <span style="color:red">The seller could check the order status. Paid or unpaid in a super clear view</span>
19. <span style="color:red">If the seller would like to know the sales performance and history in detail, our service also provide some information such as cost, unit price, sold quantity, profit, and turnover</span>
20. <span style="color:red">In our service, you could be either a buyer or seller</span>

## Installation
- Clone this project
```bash
git clone https://github.com/tn710617/FacebookOptimizedLiveStreamSellingSystem.git
```

- Enter the following in this project
```bash
composer install
```

- Create your own database

- Create `.env` file
```bash
cp .env.example .env
```

- Create your key
```
php artisan key:generate
```

- Create table
```
php artisan migrate
```

- Configure `.env` file as follows:
```bash
vim .env
```
- Configuration in `.env` in detail.
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

5. Orders
```
# How many days you prefer orders to be expired or deleted or completed.
DAYS_OF_ORDER_TO_BE_EXPIRED=3
DAYS_OF_ORDER_TO_BE_DELETED=6
DAYS_OF_ORDER_TO_BE_COMPLETED=7
```

6. Size of photos
```
# The size you prefer of the image uploaded by add-new-item function
ITEM_IMAGE_TO_BE_RESIZED_HEIGHT=416
ITEM_IMAGE_TO_BE_RESIZED_WIDTH=300
```

7. Notification
```
# The link you would like to show on notification mail to let user click and go through.
SERVICE_WEBSITE=
```

## Documentation
[Here is the API document](https://tn710617.github.io/API_Document/FacebookOptimizedSellingSystem/)

## Conclusion
It's my pleasure and honor to work with my comrades from each end. On the way, we all came across some tech difficulties, and fortunately we all went through them.
In this project, whenever anyone had a problem and delayed the schedule, there was never a blame here rather than a open discussion and a brainstorming together.
To us, we never had pressures from each other. The only source of pressure was always the standard we had for ourselves.
For me, it's not only a side project, also a meaningful and unforgettable experience in my life.

![](https://i.imgur.com/9p36cP2.jpg)



