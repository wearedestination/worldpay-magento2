<?php

namespace Worldpay\Payments\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
	public function toOptionArray() {
		return [
			['value' => 'authorize_capture', 'label' =>__('Authorize and Capture')]
			];
	}

}
