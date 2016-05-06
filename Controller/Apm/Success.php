<?php

namespace Worldpay\Payments\Controller\Apm;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;

class Success extends Apm
{
    public function execute()
    {
        $incrementId = $this->checkoutSession->getLastRealOrderId();
        
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);

        $quoteId = $order->getQuoteId();

        $this->checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);
        $this->_redirect('checkout/onepage/success');
    }
}
