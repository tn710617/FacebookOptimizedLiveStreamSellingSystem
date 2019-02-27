<!DOCTYPE html>
<html>
<head>
    <title>Facebook Login JavaScript Example</title>
    <meta charset="UTF-8">
</head>
<body>
<form action="/api/payments/2/5" method="POST">
    @csrf()
    <input type="hidden" name="cmd" value="_xclick"/>
    <label for="order_id">order_IDs</label><br>
    <input type="text" value="" name="order_id[]"><br>
    <input type="text" value="" name="order_id[]"><br>
    <input type="text" value="" name="order_id[]"><br>
    <label for="ClientBackURL">ClientBackURL</label><br>
    <input type="text" value="" name="ClintBackURL"><br>
    <input type="hidden" name="recipient_id" value="5"/>
    <button type="submit">Submit</button>
</form>
</body>
</html>
