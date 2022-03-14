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
{extends "$layout"}

{block name="content"}
    <section">
        <div
        style="
margin:0;
padding:0;
display:flex;
flex-direction:column;
align-content:center;
align-items:center;
justify-content:center;
align-self:center;"
        >
        <h2>{l s='Order summary:' mod='eupagombw'}</h2>
        <p id="eupagombw_payment_button">
            {l s='The total value of your order is:' mod='eupagombw'}
            <span id="amount" class="price">{$sign|escape:'html':'UTF-8'} {$total|escape:'html':'UTF-8'}</span>
            {if {$use_taxes} == 1}
                {l s='(C/ IVA)' mod='eupagombw'}
            {else}
                {l s='(S/ IVA)' mod='eupagombw'}
            {/if}
        </p>
        <p id="eupagombw_payment_button">
        <form action="{$link->getModuleLink('eupagombw', 'validaMbway')|escape:'html':'UTF-8'}" method="post"
              id="eupagombw" onSubmit="return validateForm()">
            <table 
                   style="width: 400px;color:#666;background-color: #ffffff;height: 50px; margin-left: auto;
  margin-right: auto;">
                <tbody style="padding: 8px;">
                <tr>
                    <td style="border: 0; padding:5px; margin:0; background-color: #D80410; color: #f1f1f1; text-align: center; font-size:small;">
                        <div align="center">{l s='Payment by MB Way' mod='eupagombw'}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px;">
                        <label>{l s='Enter the phone number associated with your MBWAY' mod='eupagombw'}</label></br>
                        <input style="width:100%;height: 30px; line-height:25px; border: 1px solid #d70510; padding: 15px;"
                               type="text" placeholder="9XXXXXXXX" pattern='^([9][1236])[0-9]*$'
                               title="{l s='Phone number incorret' mod='eupagombw'}" name="alias" checked="checked"
                               id='alias' required/>
                </tr>
                <tr style="/* padding: 19px; */">
                    <td style="border: 0; padding: 0 15px; margin:0;color: #666;font-size:small;">{l s='If you have not yet installed the application on your smartphone or have not yet joined the mbway service, please see this ' mod='eupagombw'}
                        <a target="_blank" href="https://www.mbway.pt/#como-aderir">link</a>.
        </p></td>
        </tr>
        </tbody>
        </table>
        <p>{l s='After clicking "confirm order" you will receive an order on your smartphone.' mod='eupagombw'}</p>
        <br/>
        <p  id="cart_navigation" style="
margin:0;
padding:0;
display:flex;
flex-direction:row;
align-content:center;
align-items:center;
justify-content:center;
align-self:center;">
            <a href="{$link->getPageLink('order', true)|escape:'html':'UTF-8'}?step=3"
               class="btn button_large" style = "border: 1px solid;box-shadow: 5px 5px #888888">{l s='Other Payment Methods' mod='eupagombw'}</a>
            <input type="submit" name="submit" value="{l s='Confirm Order' mod='eupagombw'}"
                   class="exclusive_large btn button_large" style = "border: 1px solid;box-shadow: 5px 5px #888888"/>
            <span id="spnPhoneStatus"></span>
            </form>
        </p>
        </div>
    </section>
    <script language="JavaScript" type="text/javascript">
        //<!--
        {literal}

        function validateForm() {
            console.log('test');
            var x = document.forms["mbway"]["alias"].value;
            console.log(x);
            var pattern = /^\d{9}$/;
            if (!x.match(pattern)) {
                msg = "O n\u00famero de telefone  introduzido n\u00e3o \u00e9 v\u00e1lido!";
                alert(msg);
                return false;
            }
        }

        {/literal}
        //-->
    </script>
{/block}


 