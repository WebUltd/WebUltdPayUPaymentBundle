<?php

/**
 *	OpenPayU Standard Library, handle Order Domain
 *
 *	@package	OpenPayU
 *	@copyright	Copyright (c) 2011-2012, PayU
 *	@license	http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 */

class OpenPayUDomain {

	private static $msg2domain = null;
	
	/**
	 * @deprecated
	 */
	private static function builder()
	{
		OpenPayUDomain::$msg2domain = array
		(
			'OrderCreateRequest' 				=> 'OrderDomainRequest',
			'OrderCreateResponse' 				=> 'OrderDomainResponse',
			'OrderStatusUpdateRequest' 			=> 'OrderDomainRequest',
			'OrderStatusUpdateResponse' 		=> 'OrderDomainResponse',
			'OrderCancelRequest' 				=> 'OrderDomainRequest',
			'OrderCancelResponse' 				=> 'OrderDomainResponse',											
			'OrderNotifyRequest' 				=> 'OrderDomainRequest',
			'OrderNotifyResponse' 				=> 'OrderDomainResponse',											
			'OrderRetrieveRequest' 				=> 'OrderDomainRequest',
			'OrderRetrieveResponse' 			=> 'OrderDomainResponse',
			'ShippingCostRetrieveRequest' 		=> 'OrderDomainRequest',
			'ShippingCostRetrieveResponse' 		=> 'OrderDomainResponse'
		);
	}

	/**
	 * Function returns Message domain
	 * @param string $msg
	 * @access public
	 * @return array
	 */
	public static function getDomain4Message($msg)
	{
		self::$msg2domain = array
		(
			'OrderCreateRequest' 				=> 'OrderDomainRequest',
			'OrderCreateResponse' 				=> 'OrderDomainResponse',
			'OrderStatusUpdateRequest' 			=> 'OrderDomainRequest',
			'OrderStatusUpdateResponse' 		=> 'OrderDomainResponse',
			'OrderCancelRequest' 				=> 'OrderDomainRequest',
			'OrderCancelResponse' 				=> 'OrderDomainResponse',											
			'OrderNotifyRequest' 				=> 'OrderDomainRequest',
			'OrderNotifyResponse' 				=> 'OrderDomainResponse',											
			'OrderRetrieveRequest' 				=> 'OrderDomainRequest',
			'OrderRetrieveResponse' 			=> 'OrderDomainResponse',
			'ShippingCostRetrieveRequest' 		=> 'OrderDomainRequest',
			'ShippingCostRetrieveResponse' 		=> 'OrderDomainResponse'
		);
		
		return self::$msg2domain[$msg];
	}
}

?>