<?php

/*
	ver. 0.1.0
	OpenPayU Standard Library, handle Order Domain

	Copyright (c) 2011-2012 PayU
	http://www.payu.com

	CHANGE_LOG:
		2011-12-20, ver. 0.1.0
		- start file
		- change name from OrderUpdateRequest -> OrderStatusUpdateRequest.

*/

class OpenPayUDomain {

	private static $msg2domain = null;

	private static function builder() {
		OpenPayUDomain::$msg2domain = array("OrderCreateRequest" 				=> "OrderDomainRequest",
											"OrderCreateResponse" 				=> "OrderDomainResponse",
											"OrderStatusUpdateRequest" 			=> "OrderDomainRequest",
											"OrderStatusUpdateResponse" 		=> "OrderDomainResponse",
											"OrderCancelRequest" 				=> "OrderDomainRequest",
											"OrderCancelResponse" 				=> "OrderDomainResponse",
											"OrderNotifyRequest" 				=> "OrderDomainRequest",
											"OrderNotifyResponse" 				=> "OrderDomainResponse",
											"OrderRetrieveRequest" 				=> "OrderDomainRequest",
											"OrderRetrieveResponse" 			=> "OrderDomainResponse",
											"ShippingCostRetrieveRequest" 		=> "OrderDomainRequest",
											"ShippingCostRetrieveResponse" 		=> "OrderDomainResponse");
	}

	public static function getDomain4Message($msg) {

		if (is_null(OpenPayUDomain::$msg2domain)) {
			OpenPayUDomain::builder();
		}

		return OpenPayUDomain::$msg2domain[$msg];
	}

}

?>