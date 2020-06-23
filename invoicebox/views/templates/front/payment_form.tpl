
<h3>Оплата через InvoiceBox</h3>
 
<form id="invoicebox-pay-form" action='https://go.invoicebox.ru/module_inbox_auto.u' method="post">

 <input type="hidden" name="itransfer_participant_id" value="{$itransfer_participant_id}" />
   <input type="hidden" name="itransfer_participant_ident" value="{$itransfer_participant_ident}" />
   <input type="hidden" name="itransfer_participant_sign" value="{$signatureValue}" />
   <input type="hidden" name="itransfer_order_id" value="{$order_id}" />
   <input type="hidden" name="itransfer_order_amount" value="{$amount}" />
   <input type="hidden" name="itransfer_order_quantity" value="{$quantity}" />
   <input type="hidden" name="itransfer_testmode" value="{$itransfer_testmode}" />
   <input type="hidden" name="itransfer_order_currency_ident" value="{$currency_code}" />
   <input type="hidden" name="itransfer_order_description" value="{$invoice}" />
   <input type="hidden" name="itransfer_person_name" value="{$customer->firstname.' '.$customer->lastname}" />
   <input type="hidden" name="itransfer_person_email" value="{$customer->email}" />
   <input type="hidden" name="itransfer_person_phone" value="{$address->phone}" />
   <input type="hidden" name="itransfer_body_type" value="PRIVATE" />
   <input type="hidden" name="itransfer_url_return" value="{'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order-history'}" />
   <input type="hidden" name="itransfer_url_notify" value="{'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/invoicebox/callback.php'}" />
   <input name="itransfer_cms_name" value="Prestashop" type="hidden">
   {$i=0}
    {foreach from=$products item=$product}
    {$i++}
   <input type="hidden" name="itransfer_item{$i}_name" value="{$product['name']}" />
   <input type="hidden" name="itransfer_item{$i}_quantity" value="{$product['quantity']}" />
   <input type="hidden" name="itransfer_item{$i}_price" value="{$product['total_wt']*$kcup/$product['quantity']}" />
   <input type="hidden" name="itransfer_item{$i}_vatrate" value="{$product['rate']}" />
   <input type="hidden" name="itransfer_item{$i}_measure" value="шт." />
    {/foreach}
	{if($shipping_cost>0)}
	{$i++}
   <input type="hidden" name="itransfer_item{$i}_name" value="Доставка" />
   <input type="hidden" name="itransfer_item{$i}_quantity" value="1" />
   <input type="hidden" name="itransfer_item{$i}_price" value="{$shipping_cost}" />
   <input type="hidden" name="itransfer_item{$i}_measure" value="шт." />
    {/if}


   

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
           // document.getElementById('invoicebox-pay-form').submit();
        </script>

