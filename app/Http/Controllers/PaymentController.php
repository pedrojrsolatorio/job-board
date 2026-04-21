<?php

namespace App\Http\Controllers;

use App\Models\{JobListing, Payment};
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class PaymentController extends Controller
{
    const PROMOTION_PRICE = 2999; // $29.99 in cents

    public function promote(Request $request, JobListing $job)
    {
        $this->authorize('update', $job);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => self::PROMOTION_PRICE,
            'currency' => 'usd',
            'metadata' => [
                'job_id' => $job->id,
                'user_id' => auth()->id(),
            ],
        ]);

        Payment::create([
            'user_id' => auth()->id(),
            'job_listing_id' => $job->id,
            'stripe_payment_intent_id' => $intent->id,
            'amount' => self::PROMOTION_PRICE,
            'status' => 'pending',
        ]);

        return view('employer.payment', [
            'clientSecret' => $intent->client_secret,
            'job' => $job,
            'price' => self::PROMOTION_PRICE / 100,
        ]);
    }

    public function success()
    {
        return view('employer.payment-success');
    }

    // Stripe webhook handler
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

            if ($payment) {
                $payment->update(['status' => 'completed']);
                $payment->jobListing->update([
                    'is_featured' => true,
                    'featured_until' => now()->addDays(30),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
