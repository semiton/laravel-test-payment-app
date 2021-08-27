<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="grid grid-cols-1 gap-6">
                <table class="w-full">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th align="right">Amount</th>
                        <th align="right">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ date('Y-m-d', $payment->asStripePaymentIntent()->created) }}</td>
                            <td align="right">{{ $payment->amount() }}</td>
                            <td align="right">
                                @if ($payment->isProcessing()) Processing @endif
                                @if ($payment->isCancelled()) Cancelled @endif
                                @if ($payment->isSucceeded()) Succeeded @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
