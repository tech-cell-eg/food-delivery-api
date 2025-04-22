<?php

namespace App\Http\Controllers\Api;

use App\Models\Card;
use App\Models\User;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class PaymentController extends Controller
{
    use ApiResponse;
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string|size:3',
            'save_card' => 'boolean',
            'use_saved_card' => 'boolean',
            'card_id' => ['nullable', Rule::requiredIf($request->use_saved_card), 'exists:cards,id']
        ]);

        try {
            $user = Auth::user();
            $metadata = ['user_id' => $user->id];

            if ($validated['use_saved_card'] ?? false) {
                $card = Card::find($validated['card_id']);
                $paymentIntent = $this->stripeService->createPaymentIntent(
                    $validated['amount'],
                    $validated['currency'],
                    array_merge($metadata, [
                        'card_id' => $card->id,
                        'payment_method_id' => $card->payment_method_id
                    ])
                );

                $paymentIntent->confirm(['payment_method' => $card->payment_method_id]);
            } else {
                $paymentIntent = $this->stripeService->createPaymentIntent(
                    $validated['amount'],
                    $validated['currency'],
                    $metadata
                );
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $validated['amount'],
                'currency' => strtoupper($validated['currency']),
                'status' => $paymentIntent->status,
                'metadata' => $metadata
            ]);

            return $this->successResponse([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'requires_action' => $paymentIntent->status === 'requires_action',
                'payment_status' => $paymentIntent->status,
                'amount' => $validated['amount'],
                'currency' => $validated['currency']
            ], 'Payment intent created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'save_card' => 'boolean',
            'payment_method_id' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            $paymentIntent = $this->stripeService->confirmPaymentIntent($validated['payment_intent_id']);

            $payment = Payment::where('payment_intent_id', $paymentIntent->id)->firstOrFail();
            $payment->update(['status' => $paymentIntent->status]);

            if (($validated['save_card'] ?? false) && ($validated['payment_method_id'] ?? false)) {
                $this->saveCard($user, $validated['payment_method_id']);
            }

            return $this->successResponse([
                'payment_status' => $paymentIntent->status,
                'payment' => $payment
            ], in_array($paymentIntent->status, ['succeeded', 'processing'])
                ? 'Payment successful'
                : 'Payment processing');

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function saveCard(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|string',
            'card_holder_name' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            $paymentMethod = $this->stripeService->attachPaymentMethodToCustomer(
                $validated['payment_method_id'],
                $user
            );

            $card = $this->createCardRecord($user, $paymentMethod, $validated['card_holder_name'] ?? null);

            return $this->successResponse([
                'card' => $card
            ], 'Card saved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function listCards(Request $request)
    {
        try {
            $user = Auth::user();
            $cards = Card::where('user_id', $user->id)->get();

            return $this->successResponse([
                'cards' => $cards
            ], 'Cards retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function createCardRecord(User $user, $paymentMethod, ?string $cardHolderName = null): Card
    {
        return Card::create([
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'brand' => $paymentMethod->card->brand,
            'last4' => $paymentMethod->card->last4,
            'exp_month' => $paymentMethod->card->exp_month,
            'exp_year' => $paymentMethod->card->exp_year,
            'card_holder_name' => $cardHolderName
        ]);
    }

}
