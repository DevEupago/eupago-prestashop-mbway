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
<table style="width:100%; padding:5px; font-size: 11px; color: #374953; margin:0 auto;">
	<tbody>
		<tr>
			<td style="font-size: 12px; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #45829F; padding:3px; background-color: #D80410; color: White; height:25px; line-height:25px" colspan="3"><div align="center">{l s='Payment by MB Way' mod='eupagombw'}</div></td>
		</tr>
		<tr style="background-color:#f1f1f1;">
			<td style="padding-top:8px;" rowspan="3"><div align="center"><img src="{$modules_dir|escape:'html':'UTF-8'}views/img/eupagombw.png" alt="euPago" /></div></td>
			<td style="padding:10px 2px 2px 2px; font-weight:bold; text-align:left">{l s='Mobile number' mod='eupagombw'}</td>
			<td style="padding:10px 2px 2px 2px;  text-align:left">{$telemovel|escape:'html':'UTF-8'}</td>
		</tr>
		<tr style="background-color:#f1f1f1;">
			<td style=" padding:2px; font-weight:bold; text-align:left">{l s='Reference' mod='eupagombw'}</td>
			<td style=" padding:2px; text-align:left">{$referencia|escape:'html':'UTF-8'}</td>
		</tr>
		<tr style="background-color:#f1f1f1;">
			<td style="padding:2px; padding-bottom:10px; font-weight:bold; text-align:left">{l s='Amount' mod='eupagombw'}</td>
			<td style="padding:2px; padding-bottom:10px; text-align:left">{Tools::displayPrice($total|escape:'html':'UTF-8')}</td>
		</tr>
	</tbody>
</table>
