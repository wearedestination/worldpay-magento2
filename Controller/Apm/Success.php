<?php

namespace Worldpay\Payments\Controller\Apm;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;


class Success extends Apm
{
    public function execute()
    {
        $incrementId = $this->checkoutSession->getLastRealOrderId();
        
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);

        $quoteId = $order->getQuoteId();

        $this->orderSender->send($order);

        $this->checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);
        $this->_redirect('checkout/onepage/success');
    }
}
