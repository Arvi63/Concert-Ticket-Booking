<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTestsTrait;

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}
