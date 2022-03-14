{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    euPago, Instituição de Pagamento Lda <suporte@eupago.pt>
*  @copyright 2016 euPago, Instituição de Pagamento Lda
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $resposta == 'OK'|escape:'html':'UTF-8'}
    <p>{l s='Your order in ' mod='eupagombw'}<span
                class="bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='is completed.' mod='eupagombw'}
    </p>
    <p>
        {l s='Your order will be sent once payment is confirmed.' mod='eupagombw'}
    </p>
    <p>
        {l s='For any question or for more information, please contact our' mod='eupagombw'}
        <span class="bold">
			{l s='Client support.' mod='eupagombw'}
		</span>
    </p>
    <p>
		<span class="bold">
			{l s='You should receive on your smartphone a request with the following data:' mod='eupagombw'}
		</span>
    </p>
    <div align="left">
        <table cellpadding="0" cellspacing="0" border="0"
               style="width: 370px; color:#666; background-color: #f1f1f1; height: 50px; margin: 10px 0; border: 0px solid #d70510;">
            <tbody>
            <tr style=" font-size:small; border:0;">
                <td style="border: 0; padding:5px; margin:0; background-color: #D80410; color: #f1f1f1; text-align: center; font-size:small;"
                    colspan="3">{l s='Payment by MB Way' mod='eupagombw'}</td>
            </tr>
            <tr style=" font-size:small; border:0;">
                <td rowspan="4" style=" font-size:small;padding: 0px 0px;vertical-align: middle; border:0; "><img
                            src="https://externo.eupago.pt/payment_logos/prestashop/logo_mbway.svg"
                            style="margin-bottom: 0px; margin-right: 0; margin-left:15px;"></td>

            </tr>
            <tr style=" font-size:small; border:0;">
                <td style="border:0; padding:10px 8px 0 8px;">{l s='Mobile number:' mod='eupagombw'}</td>
                <td style="border:0; padding:10px 8px 0 8px;">{$telemovel|escape:'htmlall':'UTF-8'}</td>
            </tr>

            <tr style=" font-size:small; border:0;">
                <td style="border:0; padding:10px 8px 0 8px;">{l s='Order number:' mod='eupagombw'}</td>
                <td style="border:0; padding:10px 8px 0 8px;">{$id_pedido|escape:'htmlall':'UTF-8'}</td>
            </tr>

            <tr style=" font-size:small; border:0;">
                <td style="border:0; padding:8px ; ">{l s='Amount:' mod='eupagombw'}</td>
                <td style="border:0; padding:8px ;">{$valor|escape:'htmlall':'UTF-8'}</td>
            </tr>
            <tr>
                <td style="text-align:center;padding-top:5px; font-size: xx-small;border:0; border-top: 0px solid #ddd; background-color: #fff; color: #666"
                    colspan="3">{l s='A payment request has been sent to your smartphone.' mod='eupagombw'}</td>
            </tr>
            </tbody>
        </table>
    </div>
{else}
    <p class="warning">
        {$resposta|escape:'htmlall':'UTF-8'}
        <span class="bold">Apoio ao Cliente</span>.
    </p>
    <p class="warning">
        Verificamos um problema com a sua encomenda. Se pensa que isto &eacute; um erro pode contatar o nosso
        <span class="bold">Apoio ao Cliente</span>.
    </p>
{/if}



