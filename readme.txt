=== LiqPay payment gateway for WooCommerce ===
Contributors: kirillbdev
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Tags: liqpay, woocommerce, payments, gateway
Requires PHP: 7.4
Tested up to: 6.6
Stable tag: 1.1.1

Plugin that adds supporting of LiqPay payment gateway to your WooCommerce store.

== Description ==

**Integrate supporting of LiqPay payment gateway to your WooCommerce store in few simple steps.**

[Documentation](https://kirillbdev.pro/docs/wc-liqpay-payments-base-setup/)

== Features ==

* Pay WooCommerce orders through LiqPay gateway.
* Ability to retry payment process after failure transaction.
* Support both PROD and SANDBOX environments.
* Ability to set language of gateway interface.
* WPML and Polylang compatibility (for gateway interface).

== Premium features ==

* Prepayment feature. You can set up different for which your customer must prepay for order. For example, customer must pay 200 UAH if selected shipping method equals Nova Poshta COD.
* Premium support.

[Buy PRO version](https://kirillbdev.pro/wc-liqpay-payments/)

== Known Issues ==

* This plugin use LiqPay payment gateway ([terms and conditions](https://www.liqpay.ua/information/terms/)) and its [acquirer API](https://www.liqpay.ua/doc/api/internet_acquiring/checkout?tab=1) to process payments.

== Installation ==

= Minimum Requirements =

* PHP 7.4 or greater is recommended
* MySQL 5.7 or greater is recommended

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of WooCommerce, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “WC LiqPay Payments” and click Search Plugins. Once you’ve found it you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading this plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains instructions on how to do this here.

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= Version 1.1.1 / (31.07.2024) =
* Added supporting of WooCommerce Checkout Blocks.

= Version 1.1.0 / (28.07.2024) =
* Added new option - "Processing mode". It allows you to set up payment process for your customers both via pay button (receipt page) or through direct redirect to gateway.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.0.1 / (15.07.2024) =
* Improved plugin architecture.
* Checked some security issues.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.0.0 / (07.05.2024) =
* Initial release