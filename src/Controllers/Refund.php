<?php

namespace PayPay\OpenPaymentAPI\Controller;

use PayPay\OpenPaymentAPI\Models\RefundPaymentPayload;
use Exception;
use PayPay\OpenPaymentAPI\Client;

class Refund extends Controller
{
    /**
     * Initializes Code class to manage creation and deletion of data for QR Code generation
     *
     * @param Client $MainInstance Instance of invoking client class
     * @param Array $auth API credentials
     */
    public function __construct($MainInstance, $auth)
    {
        parent::__construct($MainInstance, $auth);
    }
    /**
     * Refund a payment
     *
     * @param RefundPaymentPayload $payload SDK payload object
     * @return mixed
     */
    public function refundPayment($payload)
    {
        if (!($payload instanceof RefundPaymentPayload)) {
            throw new Exception("Payload not of type RefundPaymentPayload", 1);
        }
        $main = $this->MainInst;
        $url = $main->GetConfig('API_URL') . $main->GetEndpoint('REFUND');
        $options = $this->basePostOptions;
        $options['CURLOPT_TIMEOUT'] = 30;
        $data = $payload->serialize();
        $url = $main->GetConfig('API_URL') . $main->GetEndpoint('REFUND');
        $endpoint = '/v2' . $main->GetEndpoint('REFUND');
        $options = $this->HmacCallOpts('POST', $endpoint, 'application/json;charset=UTF-8;', $data);
        $mid = $this->main()->GetMid();
        if ($mid) {
            $options["HEADERS"]['X-ASSUME-MERCHANT'] = $mid;
        }
        $options['CURLOPT_TIMEOUT'] = 30;
        /** @phpstan-ignore-next-line */
        return json_decode(HttpPost($url, $data, $options), true);
    }

    /**
     * Get refund details.
     * @param String $merchantRefundId The unique refund transaction id provided by merchant
     * @return mixed
     */
    public function getRefundDetails($merchantRefundId)
    {
        $main = $this->MainInst;
        $url = $main->GetConfig('API_URL') . $main->GetEndpoint('REFUND') . "/$merchantRefundId";
        $endpoint = '/v2' . $main->GetEndpoint('REFUND') . "/$merchantRefundId";
        $options = $this->HmacCallOpts('GET', $endpoint);
        $mid = $this->main()->GetMid();
        if ($mid) {
            $options["HEADERS"]['X-ASSUME-MERCHANT'] = $mid;
        }
        /** @phpstan-ignore-next-line */
        return json_decode(HttpGet($url, [], $options), true);
    }
}
