<?php
/**
 * @package NEAR Pay
 * @version 1.0.0
 */
/*
Plugin Name: NEAR Pay
Plugin URI: http://wordpress.org/plugins/near-pay/
Description: Shortcode that allow your customers to SignIn with NEAR Wallet and make payments using NEAR token. NEAR is a decentralized application platform which is built atop the NEAR Protocol, a revolutionary public proof-of-stake blockchain which uses sharding to scale and an innovative account model to make apps similarly usable to those on today’s web.
Author: Vlodkow
Version: 1.0.0
Author URI: https://web3.in.ua/
License: GPLv2 or later
*/

add_action('admin_menu', 'near_pay_setup_menu');

function near_pay_setup_menu()
{
    add_menu_page('NEAR Pay', 'NEAR Pay', 'manage_options', 'near-pay', 'init');
}

function init()
{
    ?>
    <div class="wrap">
        <h2>NEAR Pay</h2>
        <div class="np-description">
            <p>
                <a href="https://near.org/" target="_blank"><b>NEAR</b></a>
                is a decentralized application platform which is built atop the NEAR Protocol, a revolutionary public proof-of-stake blockchain
                which uses sharding to scale and an innovative account model to make apps similarly usable to those on today’s web.
            </p>

            <p><b>Don't have NEAR wallet?</b> <br>
                You can create it in few minutes: <br>
                - Create mainnet wallet: <a href="https://wallet.near.org/create" target="_blank">https://wallet.near.org/</a><br>
                - Create testnet wallet: <a href="https://wallet.testnet.near.org/create" target="_blank">https://wallet.testnet.near.org/</a>
            </p>
            <b>To use this Plugin:</b> <br>
            1. Provide your NEAR wallet address, select Network and save changes.<br>
            2. Add shortcode to any Post or Page: <code>[near_pay]</code><br>
            <p>Additionally you can customize payment amount (5 NEAR in this example): <code>[near_pay amount=5]</code> <br>
                and text on the button: <code>[near_pay text="Make a Payment with NEAR"]</code>.</p>
            <hr class="np-divider">
        </div>

        <h2 class="np-settings-subtitle">Settings</h2>
        <form action="options.php" method="post" class="np-form"><?php
            settings_fields('near_pay');
            do_settings_sections('near_pay'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">NEAR Network: <sup>*</sup></th>
                    <td>
                        <fieldset>
                            <select name="network">
                                <option value="test" <?=(esc_attr(get_option('network')) == 'test') ? "selected" : ""?>>TestNet</option>
                                <option value="main" <?=(esc_attr(get_option('network')) == 'main') ? "selected" : ""?>>MainNet</option>
                            </select>
                            <label>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">NEAR Wallet: <sup>*</sup></th>
                    <td>
                        <fieldset>
                            <label>
                                <input name="address" type="text" id="address" value="<?php echo esc_attr(get_option('address')) ?>" />
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Show Logout button: <sup>*</sup></th>
                    <td>
                        <fieldset>
                            <label>
                                <select name="logout">
                                    <option value="" <?=(esc_attr(get_option('logout')) == '') ? "selected" : ""?>>No</option>
                                    <option value="yes" <?=(esc_attr(get_option('logout')) == 'yes') ? "selected" : ""?>>Yes</option>
                                </select>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function near_pay_settings()
{
    register_setting('near_pay', 'address');
    register_setting('near_pay', 'network');
    register_setting('near_pay', 'logout');
}

if (!is_admin() && !wp_is_json_request()) {
    function near_pay_process_shortcode($args)
    {
        $amount = $args['amount'] ?? 1;
        $text = $args['text'] ?? 'Pay ' . $amount . ' Near';
        $loginText = $args['login_text'] ?? 'LogIn with NEAR';
        if (esc_attr(get_option('address'))) {
            if (!empty($_GET['transactionHashes'])) {
                ?>
                <a class="np-sent"
                   target="_blank"
                   data-network="<?=esc_attr(get_option('network'))?>"
                   data-transaction="<?=$_GET['transactionHashes']?>"
                >Transaction sent, check in NEAR explorer.</a>
                <?php
            } else {
                if (!empty($_GET['errorMessage'])) {
                    ?><p class="np-error"><?=urldecode($_GET['errorMessage'])?>.</p><?php
                }
                ?>
                <button class="near-payment-button"
                        data-amount="<?=$amount?>"
                        data-text="<?=$text?>"
                        data-login_text="<?=$loginText?>"
                        data-address="<?=esc_attr(get_option('address'))?>"
                        data-network="<?=esc_attr(get_option('network'))?>">...
                </button>
                <?php
            }

            if (esc_attr(get_option('logout'))) {
                ?>
                <div class="np-logout">NEAR LogOut</div>
                <?php
            }
        }
    }

    add_shortcode("near_pay", "near_pay_process_shortcode");
}


function near_pay_scripts_and_styles()
{
    wp_enqueue_style('near_pay-style', plugin_dir_url(__FILE__) . 'assets/css/near-pay.css', false, false, false);
    wp_enqueue_script('near_pay-script', plugin_dir_url(__FILE__) . 'assets/js/near-pay.js', array('jquery'), false, false);
    wp_enqueue_script('near_pay-api', 'https://cdn.jsdelivr.net/npm/near-api-js@0.41.0/dist/near-api-js.min.js', array('jquery'), '0.41.0', false);
}

function near_pay_admin_scripts_and_styles()
{
    wp_enqueue_style('near_pay-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-near-pay.css', false, false, false);
}

add_action('wp_enqueue_scripts', 'near_pay_scripts_and_styles');
add_action('admin_enqueue_scripts', 'near_pay_admin_scripts_and_styles');
add_action('admin_init', 'near_pay_settings');
