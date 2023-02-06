<?php

namespace App\Service;

use Stripe\Stripe;


/**
 * Class StripeService
 * @package App\Service
 * doc checkout https://stripe.com/docs/payments/checkout/how-checkout-works
 * doc developer dashboard https://dashboard.stripe.com/test/developers
 */
class StripeService
{
    const VERSION = '2022-11-15';

    public function __construct(
        private string $stripePrivateKey
    ){ }


    public function init(?string $stripePrivateKey = null)
    {
        if($stripePrivateKey !== null) {
            $this->stripePrivateKey = $stripePrivateKey;
        }

        Stripe::setApiKey($this->stripePrivateKey);
        Stripe::setApiVersion(self::VERSION);
    }
}
