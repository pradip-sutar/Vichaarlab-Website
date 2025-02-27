<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ PhonePe API Credentials
$merchantId = "PGTESTPAYUAT69"; // Check this with PhonePe
$saltKey = "f23f3fc9-f7ca-455f-9fb3-8bd124642fdf"; // Check this with PhonePe
$saltIndex = "1";  
$callbackUrl = "https://your-public-url.com/phonepe_callback.php"; 

// ✅ Payment Details
$amount = 10000; // Amount in paise (100 INR = 10000 paise)
$transactionId = uniqid("TXN_"); // Unique transaction ID

// ✅ Request Body
$data = [
    "merchantId" => $merchantId,
    "merchantTransactionId" => $transactionId,
    "amount" => $amount,
    "merchantUserId" => "123456",
    "redirectUrl" => $callbackUrl,
    "redirectMode" => "POST",
    "callbackUrl" => $callbackUrl,
    "mobileNumber" => "9999999999",
    "paymentInstrument" => [
        "type" => "PAY_PAGE"
    ]
];

// ✅ Convert payload to JSON
$payload = json_encode($data, JSON_UNESCAPED_SLASHES);

// ✅ Correct Signature Generation
$checksumString = $payload . "/pg/v1/pay" . $saltKey;
$checksum = hash_hmac('sha256', $checksumString, $saltKey);

// ✅ API Endpoint (Preprod for testing)
$url = "https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay";

// ✅ Headers
$headers = [
    "Content-Type: application/json",
    "X-VERIFY: " . $checksum . "###" . $saltIndex,
    "X-MERCHANT-ID: " . $merchantId // Sometimes required
];

// ✅ cURL Request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute cURL request
$response = curl_exec($ch);

// ✅ Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

curl_close($ch);

// ✅ Decode API Response
$responseData = json_decode($response, true);

// ✅ Debugging: Print full API response
echo "<pre>";
print_r($responseData);
echo "</pre>";

// ✅ Redirect if success, otherwise show error
if (isset($responseData['code']) && $responseData['code'] == "SUCCESS") {
    $paymentUrl = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
    header("Location: $paymentUrl");
    exit;
} else {
    echo "<strong>Error:</strong> Payment failed!<br>";
    if (isset($responseData['message'])) {
        echo "Message: " . $responseData['message'];
    } else {
        echo "Unknown error.";
    }
}
?>
