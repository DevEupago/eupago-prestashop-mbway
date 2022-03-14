{*
*  20013-2016 euPago, instituição de pagamento LDA
*
*  @author    euPago <suporte@eupago.pt>
*  @copyright 20013-2016 euPago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="panel">
    <div class="row eupago_multibanco-header">
        <div class="col-xs-6 col-md-4 text-center">
            <img style="width: auto;" src="{$module_dir|escape:'html':'UTF-8'}views/img/logolaranja.png"
                 class="col-xs-6 col-md-4 text-center" id="payment-logo"/>
        </div>
        <div class="col-xs-6 col-md-4 text-center header2">
            <h4>{l s='euPago - payment solutions' mod='eupagombw'}</h4>
            <h4>{l s='MB WAY payment´s' mod='eupagombw'}</h4>
        </div>
        <div class="col-xs-12 col-md-4 text-center header2">
            <a href="http://www.eupago.pt/registo?lang=en&prestashop#registo_form" target="black"
               class="btn btn-primary"
               id="create-account-btn">{l s='Create an account now!' mod='eupagombw'}</a><br/>
            {l s='Already have an account?' mod='eupagombw'}<a href="https://eupago.pt/clientes/users/login"
                                                                 target="blank"> {l s='Log in' mod='eupagombw'}</a>
        </div>
    </div>

    <hr/>

    <div class="eupago_multibanco-content">
        <div class="row">
            <div class="col-md-6">
                <h5>{l s='euPago MB WAY payment offers the following benefits' mod='eupagombw'}</h5>
                <dl>
                    <dt>&middot; {l s='Increase customer local payment options' mod='eupagombw'}</dt>
                    <dd>{l s='An innovative payment solution that allows customers to make payments using your smartphone or tablet' mod='eupagombw'}</dd>

                    <dt>&middot; {l s='Help to improve cash flow' mod='eupagombw'}</dt>
                    <dd>{l s='Receive funds quickly from the bank of your choice.' mod='eupagombw'}</dd>

                    <dt>&middot; {l s='Real time callback' mod='eupagombw'}</dt>
                    <dd>{l s='With our module you will receive the payment notification in real time,' mod='eupagombw'}
                        <br>{l s='and the order status is update automatically.' mod='eupagombw'}</dd>
                </dl>
            </div>

            <div class="col-md-6">
                <h5>{l s='Check our backoffice' mod='eupagombw'}</h5>
                <iframe width="100%" height="315" src="https://www.youtube.com/embed/aZ2nrbsU20A" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12">
                <h4>{l s='Accept payments with MB Way:' mod='eupagombw'}</h4>

                <div class="row">
                    <img style="max-width:100px;" src="{$module_dir|escape:'html':'UTF-8'}/views/img/eupagombw_p.png"
                         class="col-md-6" id="payment-logo"/>
                    <div class="col-md-6">
                        <h6 style="padding: 10px 0 0 0"
                            class="text-branded">{l s='Available in Portugal ATM machines and homebanking' mod='eupagombw'}</h6>
                        <p class="text-branded">{l s='Call +351 222 061 597 if you have any questions or need more information!' mod='eupagombw'}</br>
                            <a class="link" href="https://www.mbway.pt/"
                               target="blank">{l s='What is MB Way?' mod='eupagombw'}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
