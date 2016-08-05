<?php

namespace Worldpay\Payments\Controller\Threeds;

class Process extends Threeds
{
    public function execute()
    {
        $post = $this->getRequest()->getParams();

        if (!isset($post['PaRes'])) {
            throw new \Exception('No PaRes found');
        }
        $paRes = $post['PaRes'];

        $incrementId = $this->checkoutSession->getLastRealOrderId();
        
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);

        $wordpayOrderCode = $order->getPayment()->getAdditionalInformation("worldpayOrderCode");
        $payment = $order->getPayment();


        //First authorise 3ds order
        $this->wordpayPaymentsCard->authorise3DSOrder($paRes, $order);

       
       $worldpayClass = $this->wordpayPaymentsCard->setupWorldpay();

      //  Update order
        $wpOrder = $worldpayClass->getOrder($wordpayOrderCode);

        if ($wpOrder['paymentStatus'] == 'AUTHORIZED') {
            $wpOrder['amount'] = $wpOrder['authorizedAmount'];
        }
        $amount = $wpOrder['amount']/100;
        $this->wordpayPaymentsCard->updateOrder($wpOrder['paymentStatus'], $wpOrder['orderCode'], $order, $payment, $amount);

        $this->orderSender->send($order);

        $quoteId = $order->getQuoteId();

        $this->checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        echo '<script>parent.location.href="'. $this->urlBuilder->getUrl('checkout/onepage/success', ['_secure' => true]) .'"</script>';
        exit;
    }
}
