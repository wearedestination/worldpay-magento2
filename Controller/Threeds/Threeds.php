<?php

namespace Worldpay\Payments\Controller\Threeds;
use Magento\Framework\View\LayoutFactory;
use Magento\Payment\Helper\Data as PaymentHelper;

abstract class Threeds extends \Magento\Framework\App\Action\Action
{

	/**
     * @var Session
     */
    protected $_modelSession;

    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;


    protected $wordpayPaymentsCard;
    protected $urlBuilder;
    protected $checkoutSession;
    protected $orderFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        LayoutFactory $viewLayoutFactory,
        PaymentHelper $paymentHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->urlBuilder = $context->getUrl();
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->wordpayPaymentsCard = $paymentHelper->getMethodInstance('worldpay_payments_card');
         parent::__construct($context);
    }
}
