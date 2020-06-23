<?php
class InvoiceboxValidationModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{

$cart = $this->context->cart;
		if ($cart->id_customer == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'invoicebox')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die('This payment method is not available.');

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');
		
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$cart = $this->context->cart;
		$currency = new Currency((int)($cart->id_currency));

$this->module->validateOrder((int)($cart->id), Configuration::get('PS_OS_BANKWIRE'), (float)($total), $this->module->displayName, 'Waiting of payment', array(), (int)$currency->id, false, $customer->secure_key);

$order_id = Order::getOrderByCartId($cart->id);
$order = new Order($order_id);
$order_id =$order->id;
// include(dirname(__FILE__).'/../../header.php');


$itransfer_participant_ident = $this->module->itransfer_participant_ident;

$itransfer_participant_id = $this->module->itransfer_participant_id;

$itransfer_testmode = $this->module->itransfer_testmode;



$amount = number_format($cart->getOrderTotal(true, 3), 2, '.', '');

$total = number_format($cart->getOrderTotal(true, 4), 2, '.', '');

$subtotal = number_format($cart->getOrderTotal(true, 1), 2, '.', '');

$kcup = $total / $subtotal;
    
$shipping_cost = number_format($cart->getOrderTotal(true, 5), 2, '.', '');

$currency_code = $currency->iso_code;

$invoice = "Order ".$order_id;

$invoicebox_api_key = $this->module->invoicebox_api_key;

$signatureValue = md5(
			$itransfer_participant_id.
			$order_id.
			$amount.
			$currency_code.
			$invoicebox_api_key
			); 
$quantity = $cart->nbProducts();
$address = new Address($cart->id_address_delivery);
		
			
?>

<h3>Оплата через InvoiceBox</h3>

<form id="invoicebox-pay-form" action='https://go.invoicebox.ru/module_inbox_auto.u' method="post">

 <input type="hidden" name="itransfer_participant_id" value="<?php echo $itransfer_participant_id; ?>" />
   <input type="hidden" name="itransfer_participant_ident" value="<?php echo $itransfer_participant_ident; ?>" />
   <input type="hidden" name="itransfer_participant_sign" value="<?php echo $signatureValue; ?>" />
   <input type="hidden" name="itransfer_order_id" value="<?php echo $order_id; ?>" />
   <input type="hidden" name="itransfer_order_amount" value="<?php echo $amount; ?>" />
   <input type="hidden" name="itransfer_order_quantity" value="<?php echo $quantity; ?>" />
   <input type="hidden" name="itransfer_testmode" value="<?php echo $itransfer_testmode; ?>" />
   <input type="hidden" name="itransfer_order_currency_ident" value="<?php echo $currency_code; ?>" />
   <input type="hidden" name="itransfer_order_description" value="<?php echo $invoice; ?>" />
   <input type="hidden" name="itransfer_person_name" value="<?php echo $customer->firstname.' '.$customer->lastname; ?>" />
   <input type="hidden" name="itransfer_person_email" value="<?php echo $customer->email; ?>" />
   <input type="hidden" name="itransfer_person_phone" value="<?php echo $address->phone; ?>" />
   <input type="hidden" name="itransfer_body_type" value="PRIVATE" />
   <input type="hidden" name="itransfer_url_return" value=<?php echo 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order-history'; ?> />
   <input name="itransfer_cms_name" value="Prestashop 1.7" type="hidden">
   <?php 
   $i=0;
   $products = $cart->getProducts();
    foreach ($products as $product) { 
    $i++;?>
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_name" value="<?php echo $product['name']; ?>" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_quantity" value="<?php echo $product['quantity']; ?>" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_price" value="<?php echo $product['total_wt']*$kcup/$product['quantity']; ?>" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_vatrate" value="<?php echo $product['rate']; ?>" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_measure" value="шт." />
    <?php 
	 } 
	if($shipping_cost>0){
	$i++;?>
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_name" value="Доставка" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_quantity" value="1" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_price" value="<?php echo $shipping_cost; ?>" />
   <input type="hidden" name="itransfer_item<?php echo $i; ?>_measure" value="шт." />
    <?php 
	}
	
	?>


   

	<p>
        <img src="/modules/invoicebox/invoicebox.png" alt="Оплата через InvoiceBox" />
    </p>
	<p>
		Вы выбрали оплату через InvoiceBox		
	</p>
	<p>
		<b>В течение нескольких секунд произойдет редирект, если этого не произойдет, пожалуйста, подтвердите заказ, нажав кнопку 'Подтверждаю заказ'</b>
	</p>
	
	<p class="cart_navigation">
		<input type="submit" name="m_process" value="Подтверждаю заказ" class="exclusive_large" />
	</p>

</form>
<script type="text/javascript">
            document.getElementById('invoicebox-pay-form').submit();
        </script>
<?php

// include(dirname(__FILE__).'/../../footer.php');

}
}
?>