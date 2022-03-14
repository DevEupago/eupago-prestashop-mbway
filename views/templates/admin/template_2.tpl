{*
*  20013-2016 euPago, instituição de pagamento LDA
*
*  @author    euPago <suporte@eupago.pt>
*  @copyright 20013-2016 euPago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="panel panel2">
    <div class="row eupago_multibanco-header">
        <div class="col-xs-6 col-md-4 text-center">
            <img style="width: auto;" src="{$module_dir|escape:'html':'UTF-8'}views/img/logolaranja.png"
                 class="col-xs-6 col-md-4 text-center" id="payment-logo"/>
        </div>
        <div class="col-xs-6 col-md-4 text-center header2">
            <h4>{l s='I pay...' mod='eupagombw'}</h4>
            <h4>{l s='therefore I am' mod='eupagombw'}</h4>
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

    <div class="eupagombw-content">
        <div class="row">
            <div class="col-md-7">
                <h5>{l s='About us' mod='eupagombw'}</h5>
                <p>
                    {l s='euPago is a Portuguese payment institution, accredited and supervised by the Central Bank of Portugal and specialised in providing online solutions of payments, thus being the ideal solution for any e-commerce business, from companies to private sales.' mod='eupagombw'}
                </p>
                <h5>{l s='Our payment modules and gateways' mod='eupagombw'}</h5>
                <ul class="ul-spaced">
                    <li>
                        <strong>{l s='Multibanco' mod='eupagombw'}:</strong>
                        {l s='The local payment method most used in Portugal' mod='eupagombw'}
                    </li>

                    <li>
                        <strong>{l s='Payshop' mod='eupagombw'}:</strong>
                        {l s='Portuguese payment method that allow your costumer pay the order in one payshop agent.' mod='eupagombw'}
                    </li>

                    <li>
                        <strong>{l s='Pagaqui' mod='eupagombw'}:</strong>
                        {l s='Portuguese payment method that allow your costumer pay the order in one pagaqui agent.' mod='eupagombw'}
                    </li>

                    <li>
                        <strong>{l s='MBWAY' mod='eupagombw'}:</strong>
                        {l s='An innovative payment solution that allows customers to make payments using your smartphone or tablet.' mod='eupagombw'}
                    </li>

                    <li>
                        <strong>{l s='PaysSafeCard' mod='eupagombw'}:</strong>
                        {l s='paysafecard is the world‘s leading provider of prepaid payment solutions for the internet. paysafecard allows customers to pay online as simply and safely as using cash.' mod='eupagombw'}
                    </li>

                    <li>
                        <strong>{l s='Credit Card' mod='eupagombw'}:</strong>
                        {l s='Allow payments in all SEPA region. Secure, easy and fast.' mod='eupagombw'}
                    </li>
                </ul>
            </div>

            <div class="col-md-5">
                <h5>{l s='Check our backoffice' mod='eupagombw'}</h5>
                <iframe width="100%" height="315" src="https://www.youtube.com/embed/TKP4XZrgj9Q" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <img src="{$module_dir|escape:'html':'UTF-8'}views/img/logospagamentos.png" class="col-md-6"
                         id="payment-logo"/>
                    <div class="col-md-6 text-center">
                        <h6>{l s='If you want to know more about us and our payment gateways, feel free to visit us in ' mod='eupagombw'}
                            <a href="https:www.eupago.pt">www.eupago.pt </a>{l s='or' mod='eupagombw'}{l s=' call +351 222 061 597' mod='eupagombw'} {l s='or email us to' mod='eupagombw'}
                            <a href="mailto:geral@eupago.pt">geral@eupago.pt</a></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>