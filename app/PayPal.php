<?php

namespace App;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PaypalIPN;

class PayPal extends Model {

    protected $fillable = ['txn_id', 'txn_type', 'payment_date', 'status', 'expiry_time', 'recipient_id'];

    public function orderRelations()
    {
        return $this->hasMany('App\OrderRelations', 'payment_service_order_id', 'id');
    }

    public function recipient()
    {
        return $this->hasOne('App\Recipient', 'id', 'recipient_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function make(Array $toBeSavedInfo, Request $request, Recipient $recipient)
    {
        DB::beginTransaction();
        try
        {
            $PayPal = new self();

            $PayPal->user_id = $toBeSavedInfo['user_id'];
            $PayPal->payment_service_id = $toBeSavedInfo['payment_service']->id;
            $PayPal->expiry_time = $toBeSavedInfo['expiry_time'];
            $PayPal->merchant_trade_no = $toBeSavedInfo['merchant_trade_no'];
            $PayPal->total_amount = $toBeSavedInfo['total_amount'];
            $PayPal->trade_desc = $toBeSavedInfo['trade_desc'];
            $PayPal->item_name = $toBeSavedInfo['orders_name'];
            $PayPal->mc_currency = $toBeSavedInfo['mc_currency'];
            $PayPal->recipient_id = $recipient->id;
            $PayPal->save();

            foreach ($toBeSavedInfo['orders'] as $order)
            {
                $order_relations = new OrderRelations();
                $order_relations->payment_service_id = $toBeSavedInfo['payment_service']->id;
                $order_relations->payment_service_order_id = $PayPal->id;
                $order_relations->order_id = $order->id;
                $order_relations->save();
            }
        } catch (Exception $e)
        {
            DB::rollBack();

            return 'something went wrong with DB';
        }
        DB::commit();
    }

    public function send(Array $toBeSavedInfo, Request $request, Recipient $recipient)
    {
        $enableSandbox = env('PAYPAL_SANDBOX_ENABLESANDBOX');

        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        $data = [];

        // Set the PayPal account
        $data['business'] = env('PAYPAL_SANDBOX_MAIL');

        // Set the PayPal return addresses, after the transaction is completed, the user could be back via this URL.
        $data['return'] = $toBeSavedInfo['ClientBackURL'];

        // During the transaction process on PayPal's site, the user could cancel the transaction and go back via this URL.
        $data['cancel_return'] = env('PAYPAL_SANDBOX_CANCEL_URL');

        // After the transaction is completed, PayPal will send IPN message to this URL.
        $data['notify_url'] = env('PAYPAL_SANDBOX_NOFITY_URL');

        // Set the details about the products being purchased, including the price for every individual
        // and currency so that these aren't overridden by the form data.
        $i = 1;
        foreach ($toBeSavedInfo['orders'] as $order)
        {
            $data["item_name_$i"] = $order->item_name;
            $data["item_number_$i"] = $order->quantity;
            $data["amount_$i"] = $order->total_amount;
            $i++;
        }

        $data['currency_code'] = $toBeSavedInfo['mc_currency'];

        // Add any custom fields for the query string.
        $data['custom'] = $toBeSavedInfo['merchant_trade_no'];

        // Add recipient's information
        $data['address_override'] = 1;
        $data['country'] = $recipient->country_code;
        $data['city'] = $recipient->city;
        $data['address1'] = $recipient->others;
        $data['zip'] = $recipient->postcode;
        $data['first_name'] = $recipient->name;

        // This setting allow to add multiple items with IPN method
        $data['upload'] = '1';
        $data['cmd'] = "_cart";

        // Add charset
        $data['charset'] = 'utf-8';

        // Build the query string from the data.
        $queryString = http_build_query($data);

        // Build the URL to PayPal
        $url = $paypalUrl . '?' . $queryString;

        return $url;
    }

    public function listen(Request $request)
    {
        $_POST = $request->post();

        $payment_status = $_POST['payment_status'];
        $merchant_trade_no = $_POST['custom'];
        $txn_id = $_POST['txn_id'];
        $txn_type = $_POST['txn_type'];
        $payment_date = Carbon::parse($_POST['payment_date'])->setTimezone('UTC');
        $mc_gross = $_POST['mc_gross'];
        $mc_currency = $_POST['mc_currency'];

        $enable_sandbox = env('PAYPAL_SANDBOX_ENABLESANDBOX');

// Use this to specify all of the email addresses that you have attached to paypal:
        $my_email_addresses = array(env('PAYPAL_SANDBOX_MAIL'));

// Set this to true to send a confirmation email:
        $send_confirmation_email = env('PAYPAL_SANDBOX_SEND_CONFIRMATION_EMAIL');
        $confirmation_email_address = "buybuybuygogo@gmail.com";
        $from_email_address = "test@gmail.com";

// Set this to true to save a log file:
        $save_log_file = env('PAYPAL_SANDBOX_SAVE_LOG_FILE');
        $log_file_dir = storage_path() . "/app/payment_logs";

// Here is some information on how to configure sendmail:
// http://php.net/manual/en/function.mail.php#118210

        $ipn = new PaypalIPN();
        if ($enable_sandbox)
        {
            $ipn->useSandbox();
        }
        $verified = $ipn->verifyIPN();

        $data_text = "";
        foreach ($_POST as $key => $value)
        {
            $data_text .= $key . " = " . $value . "\r\n";
        }

        $test_text = "";
        if ($_POST["test_ipn"] == 1)
        {
            $test_text = "Test ";
        }

// Check the receiver email to see if it matches your list of paypal email addresses
        $receiver_email_found = false;
        foreach ($my_email_addresses as $a)
        {
            if (strtolower($_POST["receiver_email"]) == strtolower($a))
            {
                $receiver_email_found = true;
                break;
            }
        }

        date_default_timezone_set("America/Los_Angeles");
        list($year, $month, $day, $hour, $minute, $second, $timezone) = explode(":", date("Y:m:d:H:i:s:T"));
        $date = $year . "-" . $month . "-" . $day;
        $timestamp = $date . " " . $hour . ":" . $minute . ":" . $second . " " . $timezone;
        $dated_log_file_dir = $log_file_dir . "/" . $year . "/" . $month;

        $paypal_ipn_status = "VERIFICATION FAILED";
        if ($verified)
        {
            $paypal_ipn_status = "RECEIVER EMAIL MISMATCH";
            if ($receiver_email_found)
            {
                $paypal_ipn_status = "Completed Successfully";

                $PayPal = (new PayPal())->where('merchant_trade_no', $merchant_trade_no)->first();

                if ((!PayPal::checkIfTxnIdExists($txn_id)) && ($mc_gross == $PayPal->total_amount) && ($mc_currency == $PayPal->mc_currency) && ($payment_status == 'Completed'))
                {
                    $PayPal->update(['txn_id' => $txn_id, 'txn_type' => $txn_type, 'payment_date' => $payment_date, 'status' => 1, 'expiry_time' => null]);
                    $recipient = $PayPal->recipient;

                    $orderRelations = $PayPal->orderRelations->where('payment_service_id', 2);

                    Order::updateStatus($orderRelations, $recipient, 7);

                    Helpers::mailWhenPaid($PayPal, $orderRelations);
                }
            }
        } elseif ($enable_sandbox)
        {
            if ($_POST["test_ipn"] != 1)
            {
                $paypal_ipn_status = "RECEIVED FROM LIVE WHILE SANDBOXED";
            }
        } elseif ($_POST["test_ipn"] == 1)
        {
            $paypal_ipn_status = "RECEIVED FROM SANDBOX WHILE LIVE";
        }

        if ($save_log_file)
        {
            // Create log file directory
            if (!is_dir($dated_log_file_dir))
            {
                if (!file_exists($dated_log_file_dir))
                {
                    mkdir($dated_log_file_dir, 0777, true);
                    if (!is_dir($dated_log_file_dir))
                    {
                        $save_log_file = false;
                    }
                } else
                {
                    $save_log_file = false;
                }
            }
            // Restrict web access to files in the log file directory
            $htaccess_body = "RewriteEngine On" . "\r\n" . "RewriteRule .* - [L,R=404]";
            if ($save_log_file && (!is_file($log_file_dir . "/.htaccess") || file_get_contents($log_file_dir . "/.htaccess") !== $htaccess_body))
            {
                if (!is_dir($log_file_dir . "/.htaccess"))
                {
                    file_put_contents($log_file_dir . "/.htaccess", $htaccess_body);
                    if (!is_file($log_file_dir . "/.htaccess") || file_get_contents($log_file_dir . "/.htaccess") !== $htaccess_body)
                    {
                        $save_log_file = false;
                    }
                } else
                {
                    $save_log_file = false;
                }
            }
            if ($save_log_file)
            {
                // Save data to text file
                file_put_contents($dated_log_file_dir . "/" . $test_text . "paypal_ipn_" . $date . ".txt", "paypal_ipn_status = " . $paypal_ipn_status . "\r\n" . "paypal_ipn_date = " . $timestamp . "\r\n" . $data_text . "\r\n", FILE_APPEND);
            }
        }

        if ($send_confirmation_email)
        {
            // Send confirmation email
            mail($confirmation_email_address, $test_text . "PayPal IPN : " . $paypal_ipn_status, "paypal_ipn_status = " . $paypal_ipn_status . "\r\n" . "paypal_ipn_date = " . $timestamp . "\r\n" . $data_text, "From: " . $from_email_address);
        }

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly
        header("HTTP/1.1 200 OK");

    }

    public static function checkIfTxnIdExists($txn_id)
    {
        if ((PayPal::where('txn_id', $txn_id)->count()) == 0)
        {
            return false;
        }

        return true;
    }
}
