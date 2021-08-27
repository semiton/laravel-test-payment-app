<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pay form') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form id="pay-form" action="{{ route('pay_method') }}" method="post">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="amount" value="{{ __('Charge Amount:') }}" />
                                <x-jet-input id="amount" type="number" name="amount" type="text" class="mt-1 block w-full" value="100" required />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <x-jet-label for="card-holder-name" value="{{ __('Card Holder Name') }}" />
                                <x-jet-input id="card-holder-name" type="text" class="mt-1 block w-full" autocomplete="cc-name" />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <div id="card-element" class="field"></div>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <div id="card-errors" class="mt-3 text-sm text-red-600">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <x-jet-button id="card-button">
                            {{ __('Pay') }}
                        </x-jet-button>
                    </div>
                </form>
                <script src="https://js.stripe.com/v3/"></script>
                <script>
                    var stripe = Stripe('{{ config('services.stripe.key') }}');
                    var elements = stripe.elements();
                    var style = {
                        base: {
                            color: '#32325d',
                            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '16px',
                            '::placeholder': {
                                color: '#aab7c4'
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    };
                    var card = elements.create('card', {hidePostalCode: true,
                        style: style});
                    card.mount('#card-element');
                    card.addEventListener('change', function(event) {
                        var displayError = document.getElementById('card-errors');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                        } else {
                            displayError.textContent = '';
                        }
                    });
                    const cardHolderName = document.getElementById('card-holder-name');
                    const cardButton = document.getElementById('card-button');
                    const clientSecret = '{{ $intent->client_secret }}';
                    cardButton.addEventListener('click', async (e) => {
                        e.preventDefault();
                        console.log("attempting");
                        // const { setupIntent, error } = await stripe.confirmCardSetup(
                        //     clientSecret, {
                        //         payment_method: {
                        //             card: card,
                        //             billing_details: { name: cardHolderName.value }
                        //         }
                        //     }
                        // );
                        const { paymentMethod, error } = await stripe.createPaymentMethod(
                            'card', card, {
                                billing_details: { name: cardHolderName.value }
                            }
                        );
                        if (error) {
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = error.message;
                        } else {
                            paymentMethodHandler(paymentMethod);
                        }
                    });
                    function paymentMethodHandler(paymentMethod) {
                        var form = document.getElementById('pay-form');
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'payment_method');
                        hiddenInput.setAttribute('value', paymentMethod.id);
                        form.appendChild(hiddenInput);
                        console.log(paymentMethod);
                        // form.action += setupIntent.id;
                        form.submit();
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
