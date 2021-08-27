<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/payments', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        \Stripe\Stripe::setApiKey(config('services.stripe.key'));
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        /** @var \Laravel\Cashier\Payment[] $payments */
        $payments = [];
        $intents = $stripe->paymentIntents->all(['customer' => $user->asStripeCustomer()->id]);
        foreach ($intents as $intent) {
            $payments[] = new \Laravel\Cashier\Payment($intent);
        }

        return view('payments', [
            'payments' => $payments,
        ]);
    })->name('payments');

    Route::get('/pay', function (Request $request) {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $intent = $user->createSetupIntent();

        return view('pay', [
            'intent' => $intent,
        ]);
    })->name('pay');

    Route::post('/pay-process', function (Request $request) {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $input = $request->validate([
            'payment_method' => ['required'],
            'amount' => ['required', 'numeric'],
        ]);
        try {
            $payment = $user->charge((float)$input['amount'] * 100, $input['payment_method']);
            return redirect(route('pay_result', [
                'id' => $payment->asStripePaymentIntent()->id,
            ]));
        } catch (\Exception $e) {}

        return redirect(route('pay'));
    })->name('pay_method');

    Route::get('/pay-result/{id}', function (string $id, Request $request) {
        \Stripe\Stripe::setApiKey(config('services.stripe.key'));
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $intent = $stripe->paymentIntents->retrieve($id);
        $payment = new \Laravel\Cashier\Payment($intent);

        return view('pay-result', [
            'payment' => $payment,
        ]);
    })->name('pay_result');
});
