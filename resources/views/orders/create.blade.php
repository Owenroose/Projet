@extends('layouts.app')

@section('title', 'Finaliser ma commande - Nova Tech Bénin')
@section('description', 'Finalisez votre commande et recevez vos produits rapidement. Matériel informatique de qualité au Bénin.')

@push('styles')
<style type="text/tailwindcss">
    @layer components {
        .order-hero {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(30, 58, 138, 0.98) 100%);
            backdrop-filter: blur(10px);
        }

        .progress-step {
            @apply flex items-center justify-center w-12 h-12 rounded-full font-bold text-sm transition-all duration-300;
        }

        .progress-step.active {
            @apply bg-white text-slate-800 shadow-lg scale-110;
        }

        .progress-step.inactive {
            @apply bg-white/20 text-white/70;
        }

        .progress-line {
            @apply flex-1 h-0.5 mx-4 transition-all duration-300;
        }

        .progress-line.completed {
            @apply bg-white;
        }

        .progress-line.pending {
            @apply bg-white/30;
        }

        .form-card {
            @apply bg-white rounded-3xl shadow-2xl p-8 lg:p-12 transition-all duration-300;
        }

        .input-group {
            @apply relative mb-6;
        }

        .input-label {
            @apply block text-sm font-medium text-slate-700 mb-2;
        }

        .input-field {
            @apply w-full px-4 py-3 border border-gray-200 rounded-xl transition-all duration-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none;
        }

        .input-field:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .product-card {
            @apply bg-gray-50 rounded-xl p-4 border border-gray-100 transition-all duration-300 hover:shadow-md hover:border-indigo-200;
        }

        .quantity-controls {
            @apply flex items-center justify-center;
        }

        .quantity-btn {
            @apply w-8 h-8 flex items-center justify-center bg-gray-200 border border-gray-300 text-gray-700 font-bold transition-all duration-200 hover:bg-indigo-500 hover:text-white hover:border-indigo-500;
        }

        .quantity-input {
            @apply w-16 h-8 text-center border-t border-b border-gray-300 focus:border-indigo-500 focus:outline-none;
        }

        .summary-card {
            @apply bg-white rounded-3xl shadow-2xl p-8 sticky top-8;
        }

        .summary-item {
            @apply flex justify-between items-center py-3 border-b border-gray-100;
        }

        .summary-total {
            @apply flex justify-between items-center pt-6 mt-6 border-t-2 border-gray-200;
        }

        .nova-btn-order {
            @apply w-full bg-gradient-to-r from-indigo-600 to-sky-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg transition-all duration-300 hover:from-indigo-700 hover:to-sky-600 hover:shadow-xl transform hover:scale-105;
        }

        .fedapay-btn {
            @apply w-full bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg transition-all duration-300 hover:from-emerald-700 hover:to-teal-600 hover:shadow-xl transform hover:scale-105;
        }

        .shipping-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium;
        }

        .shipping-free {
            @apply bg-emerald-100 text-emerald-800;
        }

        .shipping-half {
            @apply bg-amber-100 text-amber-800;
        }

        .shipping-full {
            @apply bg-slate-100 text-slate-800;
        }

        /* Nouveaux styles pour le formulaire */
        .form-section-title {
            @apply flex items-center mb-6 text-xl font-semibold text-gray-800;
        }

        .form-section-title i {
            @apply mr-3 text-2xl text-indigo-500;
        }

        .input-group label {
            @apply text-sm font-semibold text-gray-600 mb-1;
        }

        .input-group .input-field {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg transition-colors duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100;
        }

        /* Animation loading */
        .loading-overlay {
            @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
        }

        .loading-content {
            @apply bg-white rounded-lg p-8 text-center;
        }

        .spinner {
            @apply animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-4;
        }
    }
</style>
@endpush

@section('content')
<section class="order-hero py-16 text-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Finaliser votre commande</h1>
            <p class="text-xl text-white/90 font-light">Quelques informations et votre commande sera traitée</p>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="progress-step active">1</div>
                    <span class="ml-3 text-white font-semibold">Informations</span>
                </div>
                <div class="progress-line pending"></div>
                <div class="flex items-center">
                    <div class="progress-step inactive">2</div>
                    <span class="ml-3 text-white/70">Paiement</span>
                </div>
                <div class="progress-line pending"></div>
                <div class="flex items-center">
                    <div class="progress-step inactive">3</div>
                    <span class="ml-3 text-white/70">Livraison</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">

            <div class="lg:col-span-2">
                <div class="form-card">
                    <div class="form-section-title">
                        <i class="fas fa-address-card"></i>
                        Informations de contact
                    </div>

                    <form action="{{ route('orders.store') }}" method="POST" id="order-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="input-group">
                                <label for="name" class="input-label">Nom & Prénoms *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                       class="input-field" placeholder="Ex: Koffi Jean-Baptiste" required>
                                @error('name')<p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                            </div>

                            <div class="input-group">
                                <label for="phone" class="input-label">Numéro de téléphone *</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                       class="input-field" placeholder="Ex: 97 00 00 00" required>
                                @error('phone')<p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="email" class="input-label">Email (optionnel)</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="input-field" placeholder="votre@email.com">
                            @error('email')<p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                        </div>

                        <hr class="my-8 border-gray-200">

                        <div class="form-section-title">
                            <i class="fas fa-shipping-fast"></i>
                            Adresse de livraison
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="input-group">
                                <label for="city" class="input-label">Ville de livraison *</label>
                                <select id="city" name="city" class="input-field" required>
                                    <option value="">Sélectionnez votre ville</option>
                                    <option value="Cotonou" data-price="1500" {{ old('city') == 'Cotonou' ? 'selected' : '' }}>Cotonou</option>
                                    <option value="Calavi" data-price="1500" {{ old('city') == 'Calavi' ? 'selected' : '' }}>Abomey-Calavi</option>
                                    <option value="Porto-Novo" data-price="3000" {{ old('city') == 'Porto-Novo' ? 'selected' : '' }}>Porto-Novo</option>
                                    <option value="Ouidah" data-price="3000" {{ old('city') == 'Ouidah' ? 'selected' : '' }}>Ouidah</option>
                                    <option value="Centre/Nord" data-price="4000" {{ old('city') == 'Centre/Nord' ? 'selected' : '' }}>Villes du Centre et du Nord</option>
                                </select>
                                @error('city')<p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                            </div>

                            <div class="input-group">
                                <label for="address" class="input-label">Adresse complète *</label>
                                <input type="text" id="address" name="address" value="{{ old('address') }}"
                                       class="input-field" placeholder="Ex: Quartier Fidjrossè, maison bleue" required>
                                @error('address')<p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 -mt-4 mb-8">Plus l'adresse est précise, plus la livraison sera rapide</p>

                        @if(count($cartItems) === 1)
                            <input type="hidden" name="product_id" value="{{ $cartItems[0]['product']->id }}">
                        @endif

                        <div class="bg-gradient-to-r from-indigo-50 to-sky-50 p-6 rounded-xl mb-8">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-truck text-indigo-600 text-2xl mr-3"></i>
                                <h3 class="text-lg font-bold text-gray-900">Informations de livraison</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-gift text-emerald-500 mr-2"></i>
                                    <div>
                                        <p class="font-semibold">Livraison Gratuite</p>
                                        <p class="text-gray-600">Commandes < 50 000 CFA</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-truck text-amber-500 mr-2"></i>
                                    <div>
                                        <p class="font-semibold">Frais selon la ville</p>
                                        <p class="text-gray-600">Commandes ≥ 50 000 CFA</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-sky-500 mr-2"></i>
                                    <div>
                                        <p class="font-semibold">24-48h</p>
                                        <p class="text-gray-600">Délai de livraison</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-purple-500 mr-2"></i>
                                    <div>
                                        <p class="font-semibold">Plusieurs villes</p>
                                        <p class="text-gray-600">Cotonou, Calavi, Porto-Novo...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submit-button" class="fedapay-btn">
                            <i class="fas fa-lock mr-3"></i>
                            <span id="button-text">Payer en toute sécurité</span>
                        </button>

                        <div id="loading-spinner" class="mt-4 text-center hidden">
                            <div class="spinner"></div>
                            <span class="text-gray-600">Redirection vers FedaPay...</span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="summary-card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Récapitulatif</h3>
                        @if(count($cartItems) > 1)
                            <a href="{{ route('cart.show') }}" class="text-sm font-medium text-indigo-600 hover:underline transition-colors">
                                <i class="fas fa-edit mr-1"></i>Modifier
                            </a>
                        @endif
                    </div>

                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                            <div class="product-card">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : asset('images/product-placeholder.jpg') }}"
                                         alt="{{ $item['product']->name }}"
                                         class="w-16 h-16 rounded-lg object-cover shadow-md">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $item['product']->name }}</h4>
                                        <p class="text-gray-600 text-xs">{{ number_format($item['product']->price, 0, ',', ' ') }} CFA</p>

                                        <div class="flex items-center justify-between mt-2">
                                            <div class="quantity-controls">
                                                <button type="button" class="quantity-btn rounded-l" data-action="decrement" data-product-id="{{ $item['product']->id }}">-</button>
                                                <input type="number" name="quantities[{{ $item['product']->id }}]"
                                                       value="{{ $item['quantity'] }}" min="1" max="99"
                                                       class="quantity-input" data-product-id="{{ $item['product']->id }}">
                                                <button type="button" class="quantity-btn rounded-r" data-action="increment" data-product-id="{{ $item['product']->id }}">+</button>
                                            </div>
                                            <span class="font-bold text-indigo-600 text-sm product-subtotal" data-product-id="{{ $item['product']->id }}">{{ number_format($item['subtotal'], 0, ',', ' ') }} CFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3">
                        <div class="summary-item">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-semibold" id="sub-total-display">{{ number_format($totalAmount, 0, ',', ' ') }} CFA</span>
                        </div>

                        <div class="summary-item">
                            <span class="text-gray-600 flex items-center">
                                Frais de livraison
                                <span id="shipping-badge" class="ml-2 shipping-badge hidden">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span id="shipping-status"></span>
                                </span>
                            </span>
                            <span class="font-semibold" id="shipping-fee">Sélectionnez une ville</span>
                        </div>

                        <div class="summary-total">
                            <span class="text-xl font-bold text-gray-900">Total à payer</span>
                            <span class="text-2xl font-bold text-emerald-600" id="grand-total">{{ number_format($totalAmount, 0, ',', ' ') }} CFA</span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-shield-alt text-emerald-500 mr-2"></i>
                            Paiement sécurisé
                        </h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-credit-card text-blue-500 mr-2 w-4"></i>
                                <span>Cartes Visa/Mastercard</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-mobile-alt text-green-500 mr-2 w-4"></i>
                                <span>Mobile Money (MTN, Moov)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave text-orange-500 mr-2 w-4"></i>
                                <span>Paiement à la livraison</span>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 border-t pt-2">
                            <i class="fas fa-lock mr-1"></i>
                            Paiement 100% sécurisé avec FedaPay
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="loading-overlay" class="loading-overlay hidden">
    <div class="loading-content">
        <div class="spinner"></div>
        <h3 class="text-lg font-semibold mb-2">Traitement en cours...</h3>
        <p class="text-gray-600">Veuillez patienter, redirection vers la page de paiement</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const form = document.getElementById('order-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const loadingOverlay = document.getElementById('loading-overlay');
    const citySelect = document.getElementById('city');
    const shippingFeeSpan = document.getElementById('shipping-fee');
    const grandTotalSpan = document.getElementById('grand-total');
    const subTotalDisplay = document.getElementById('sub-total-display');
    const shippingBadge = document.getElementById('shipping-badge');
    const shippingStatus = document.getElementById('shipping-status');

    // Prix des produits pour les calculs JavaScript
    const productPrices = {
        @foreach($cartItems as $item)
            {{ $item['product']->id }}: {{ $item['product']->price }},
        @endforeach
    };

    const quantityInputs = document.querySelectorAll('.quantity-input');
    const quantityButtons = document.querySelectorAll('.quantity-btn');

    /**
     * Calcule le sous-total de tous les produits
     */
    function calculateSubtotal() {
        let newSubtotal = 0;
        quantityInputs.forEach(input => {
            const productId = input.dataset.productId;
            const quantity = parseInt(input.value) || 0;
            const productTotal = productPrices[productId] * quantity;
            newSubtotal += productTotal;

            // Mettre à jour le sous-total individuel du produit
            const subtotalElement = document.querySelector(`.product-subtotal[data-product-id="${productId}"]`);
            if (subtotalElement) {
                subtotalElement.textContent = productTotal.toLocaleString('fr-FR') + ' CFA';
            }
        });
        return newSubtotal;
    }

    /**
     * Calcule les frais de livraison selon le contrôleur PHP
     * LOGIQUE EXACTE : Livraison gratuite pour commandes < 50 000 CFA
     */
    function calculateShippingFee(baseFee, orderTotal) {
        if (orderTotal < 50000) {
            // Livraison gratuite pour les commandes inférieures à 50 000 CFA
            return { fee: 0, status: 'Gratuite', class: 'shipping-free' };
        } else {
            // Application des frais normaux pour les commandes de 50 000 CFA et plus
            return { fee: baseFee, status: 'Payante', class: 'shipping-full' };
        }
    }

    /**
     * Met à jour tous les totaux et affichages
     */
    function updateTotals() {
        const currentSubtotal = calculateSubtotal();
        const selectedOption = citySelect.options[citySelect.selectedIndex];
        const baseShippingFee = parseInt(selectedOption.getAttribute('data-price')) || 0;

        subTotalDisplay.textContent = currentSubtotal.toLocaleString('fr-FR') + ' CFA';

        if (citySelect.value && baseShippingFee > 0) {
            const shippingInfo = calculateShippingFee(baseShippingFee, currentSubtotal);
            const newGrandTotal = currentSubtotal + shippingInfo.fee;

            shippingFeeSpan.textContent = shippingInfo.fee.toLocaleString('fr-FR') + ' CFA';
            grandTotalSpan.textContent = newGrandTotal.toLocaleString('fr-FR') + ' CFA';

            // Afficher le badge de statut de livraison
            shippingBadge.className = `ml-2 shipping-badge ${shippingInfo.class}`;
            shippingStatus.textContent = shippingInfo.status;
            shippingBadge.classList.remove('hidden');
        } else {
            shippingFeeSpan.textContent = 'Sélectionnez une ville';
            grandTotalSpan.textContent = currentSubtotal.toLocaleString('fr-FR') + ' CFA';
            shippingBadge.classList.add('hidden');
        }
    }

    /**
     * Gestion des boutons de quantité
     */
    quantityButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let quantity = parseInt(input.value) || 1;

            if (this.dataset.action === 'decrement' && quantity > 1) {
                quantity--;
            } else if (this.dataset.action === 'increment' && quantity < 99) {
                quantity++;
            }

            input.value = quantity;
            updateTotals();
        });
    });

    /**
     * Gestion des changements de quantité via les inputs
     */
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            let value = parseInt(this.value) || 1;
            if (value < 1) value = 1;
            if (value > 99) value = 99;
            this.value = value;
            updateTotals();
        });

        input.addEventListener('input', function() {
            updateTotals();
        });
    });

    /**
     * Gestion du changement de ville
     */
    citySelect.addEventListener('change', function() {
        updateTotals();
        validateField(this);
    });

    /**
     * Validation visuelle des champs
     */
    function validateField(field) {
        const value = field.value.trim();
        if (field.hasAttribute('required') && !value) {
            field.classList.add('border-red-500', 'bg-red-50');
            field.classList.remove('border-green-500', 'bg-green-50');
            return false;
        } else if (value) {
            field.classList.add('border-green-500', 'bg-green-50');
            field.classList.remove('border-red-500', 'bg-red-50');
            return true;
        } else {
            field.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
            return true;
        }
    }

    /**
     * Validation temps réel des champs requis
     */
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });

        field.addEventListener('input', function() {
            if (this.value.trim()) {
                validateField(this);
            }
        });
    });

    /**
     * Affichage des messages d'erreur
     */
    function showMessage(message, type = 'error') {
        const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';
        const icon = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';

        const messageDiv = document.createElement('div');
        messageDiv.className = `fixed top-24 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
        messageDiv.innerHTML = `<i class="${icon} mr-2"></i>${message}`;
        document.body.appendChild(messageDiv);

        // Animation d'entrée
        setTimeout(() => {
            messageDiv.classList.add('translate-x-0');
        }, 100);

        // Suppression automatique
        setTimeout(() => {
            messageDiv.style.transform = 'translateX(100%)';
            setTimeout(() => {
                messageDiv.remove();
            }, 300);
        }, 5000);
    }

    /**
     * Soumission du formulaire principal avec gestion complète
     */
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log('Soumission du formulaire...');

            // Validation des champs requis
            const requiredFieldNames = ['name', 'phone', 'address', 'city'];
            let isValid = true;
            let firstInvalidField = null;

            requiredFieldNames.forEach(fieldName => {
                const input = document.getElementById(fieldName);
                if (input && !validateField(input)) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = input;
                }
            });

            if (!isValid) {
                if (firstInvalidField) firstInvalidField.focus();
                showMessage('Veuillez remplir tous les champs obligatoires');
                return;
            }

            // Validation du numéro de téléphone (basique)
            const phone = document.getElementById('phone').value.trim();
            if (phone.length < 8) {
                document.getElementById('phone').focus();
                showMessage('Veuillez saisir un numéro de téléphone valide');
                return;
            }

            // Validation de l'email s'il est fourni
            const email = document.getElementById('email').value.trim();
            if (email && !isValidEmail(email)) {
                document.getElementById('email').focus();
                showMessage('Veuillez saisir un email valide');
                return;
            }

            // Interface de chargement
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                if (buttonText) buttonText.textContent = 'Traitement...';
            }
            if (loadingOverlay) loadingOverlay.classList.remove('hidden');

            // Préparation des données du formulaire
            const formData = new FormData(form);

            // Envoi de la requête AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Une erreur est survenue');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse du serveur:', data);

                if (data.success && data.redirectUrl) {
                    // Redirection vers FedaPay
                    showMessage('Redirection vers la page de paiement...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirectUrl;
                    }, 1000);
                } else {
                    throw new Error(data.error || 'Erreur lors de la création de la commande');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);

                // Restaurer l'interface
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    if (buttonText) buttonText.textContent = 'Payer en toute sécurité';
                }
                if (loadingOverlay) loadingOverlay.classList.add('hidden');

                // Afficher l'erreur
                showMessage(error.message || 'Une erreur est survenue. Veuillez réessayer.');
            });
        });
    }

    /**
     * Fonction utilitaire pour valider l'email
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Initialisation des totaux au chargement de la page
     */
    updateTotals();

    /**
     * Gestion des événements de redimensionnement pour la responsivité
     */
    window.addEventListener('resize', function() {
        // Ajuster la position des éléments fixes si nécessaire
        const summaryCard = document.querySelector('.summary-card');
        if (summaryCard && window.innerWidth < 1024) {
            summaryCard.classList.remove('sticky', 'top-8');
        } else if (summaryCard) {
            summaryCard.classList.add('sticky', 'top-8');
        }
    });

    /**
     * Validation en temps réel du formulaire
     */
    form.addEventListener('input', function(e) {
        if (e.target.matches('input[required], select[required]')) {
            validateField(e.target);
        }
    });

    /**
     * Prévention de la double soumission
     */
    let isSubmitting = false;
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
    });

    /**
     * Auto-formatage du numéro de téléphone
     */
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Supprimer tout sauf les chiffres

            // Limiter à 8 chiffres
            if (value.length > 8) {
                value = value.substr(0, 8);
            }

            // Formater avec des espaces
            if (value.length >= 2) {
                value = value.replace(/(\d{2})(\d{0,2})(\d{0,2})(\d{0,2})/, '$1 $2 $3 $4').trim();
            }

            e.target.value = value;
        });
    }

    /**
     * Gestion des raccourcis clavier
     */
    document.addEventListener('keydown', function(e) {
        // Échapper pour fermer les overlays
        if (e.key === 'Escape') {
            if (loadingOverlay && !loadingOverlay.classList.contains('hidden')) {
                // Ne pas permettre de fermer pendant le traitement
                return;
            }
        }

        // Entrée pour soumettre le formulaire si tous les champs requis sont remplis
        if (e.key === 'Enter' && e.ctrlKey) {
            const allRequired = Array.from(document.querySelectorAll('input[required], select[required]'));
            const allFilled = allRequired.every(field => field.value.trim());

            if (allFilled) {
                form.requestSubmit();
            }
        }
    });

    /**
     * Sauvegarde automatique des données dans sessionStorage (si supporté)
     */
    function saveFormData() {
        if (typeof(Storage) !== "undefined") {
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                address: document.getElementById('address').value,
                city: document.getElementById('city').value
            };
            sessionStorage.setItem('nova_order_form', JSON.stringify(formData));
        }
    }

    /**
     * Restauration automatique des données depuis sessionStorage
     */
    function loadFormData() {
        if (typeof(Storage) !== "undefined") {
            const savedData = sessionStorage.getItem('nova_order_form');
            if (savedData) {
                try {
                    const formData = JSON.parse(savedData);
                    Object.keys(formData).forEach(key => {
                        const field = document.getElementById(key);
                        if (field && !field.value) {
                            field.value = formData[key];
                        }
                    });
                    updateTotals();
                } catch (e) {
                    console.log('Erreur lors de la restauration des données:', e);
                }
            }
        }
    }

    /**
     * Sauvegarde périodique des données
     */
    const saveInterval = setInterval(saveFormData, 30000); // Toutes les 30 secondes

    // Sauvegarder aussi lors des changements
    ['name', 'phone', 'email', 'address', 'city'].forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('blur', saveFormData);
        }
    });

    // Nettoyer à la fermeture de la page
    window.addEventListener('beforeunload', function() {
        clearInterval(saveInterval);
        // Ne pas sauvegarder si le formulaire a été soumis avec succès
        if (!isSubmitting) {
            saveFormData();
        }
    });

    // Charger les données sauvegardées au démarrage
    loadFormData();

    console.log('Script de commande initialisé avec succès');
});
</script>
@endpush
