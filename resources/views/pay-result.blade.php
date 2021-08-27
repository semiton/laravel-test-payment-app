<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <span>Amount: </span>
                            <strong>{{ $payment->amount() }}</strong>
                        </div><div class="col-span-6 sm:col-span-4">
                            <span>Status: </span>
                            <strong>
                                @if ($payment->isProcessing()) Processing @endif
                                @if ($payment->isCancelled()) Cancelled @endif
                                @if ($payment->isSucceeded()) Succeeded @endif
                            </strong>
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-nav-link href="{{ route('payments') }}">
                                {{ __('Back to Payments') }}
                            </x-jet-nav-link>
                        </div>
                        <div class="col-span-6 sm:col-span-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
