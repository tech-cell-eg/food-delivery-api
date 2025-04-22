<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use App\Models\User;
use App\Models\Payment;
use App\Models\Card;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(float $amount, string $currency, array $metadata = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->formatAmount($amount),
                'currency' => strtolower($currency),
                'payment_method_types' => ['card'],
                'metadata' => $metadata
            ]);

            return $paymentIntent;
        } catch (ApiErrorException $e) {
            throw $e;
        }
    }

    public function confirmPaymentIntent(string $paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw $e;
        }
    }

    public function createPaymentMethod(array $cardDetails)
    {
        try {
            return PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => $cardDetails['number'],
                    'exp_month' => $cardDetails['exp_month'],
                    'exp_year' => $cardDetails['exp_year'],
                    'cvc' => $cardDetails['cvc'],
                ],
            ]);
        } catch (ApiErrorException $e) {
            throw $e;
        }
    }

    public function attachPaymentMethodToCustomer(string $paymentMethodId, User $user)
    {
        try {
            $customer = $this->getOrCreateCustomer($user);

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customer->id]);

            if (!$customer->invoice_settings->default_payment_method) {
                Customer::update($customer->id, [
                    'invoice_settings' => ['default_payment_method' => $paymentMethodId]
                ]);
            }

            return $paymentMethod;
        } catch (ApiErrorException $e) {
            throw $e;
        }
    }

    protected function getOrCreateCustomer(User $user)
    {
        if ($user->stripe_customer_id) {
            try {
                return Customer::retrieve($user->stripe_customer_id);
            } catch (ApiErrorException $e) {
                $user->stripe_customer_id = null;
                $user->save();
            }
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => ['user_id' => $user->id]
        ]);

        $user->stripe_customer_id = $customer->id;
        $user->save();

        return $customer;
    }

    protected function formatAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }
}