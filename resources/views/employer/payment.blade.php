@extends('layouts.app')
@section('content')
<div class='max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow'>
    <h2 class='text-2xl font-bold mb-2'>Feature Your Job Listing</h2>
    <p class='text-gray-600 mb-6'>Promote "{{ $job->title }}" for ${{ $price }}/month</p>

    <div id='payment-element' class='mb-4'></div>
    <button id='submit-btn'
            class='w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700'>
        Pay ${{ $price }}
    </button>
    <div id='payment-message' class='text-red-600 mt-3 hidden'></div>
</div>

<script src='https://js.stripe.com/v3/'></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
const elements = stripe.elements({ clientSecret: '{{ $clientSecret }}' });
const paymentEl = elements.create('payment');
paymentEl.mount('#payment-element');

document.getElementById('submit-btn').addEventListener('click', async () => {
    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: { return_url: '{{ route('payments.success') }}' },
    });
    if (error) {
        document.getElementById('payment-message').textContent = error.message;
        document.getElementById('payment-message').classList.remove('hidden');
    }
});
</script>
@endsection
