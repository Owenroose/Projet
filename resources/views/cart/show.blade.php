@extends('layouts.app')

@section('title', 'Mon Panier - Nova Tech Bénin')
@section('description', 'Consultez et modifiez votre panier d\'achats. Matériel informatique de qualité au Bénin.')

@push('styles')
<style>
    .cart-item {
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .quantity-control {
        transition: all 0.2s ease;
    }

    .quantity-control:hover {
        background-color: #f3f4f6;
    }

    .remove-btn:hover {
        background-color: #fecaca;
        color: #dc2626;
        transform: scale(1.1);
    }

    .empty-cart {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .cart-summary {
        position: sticky;
        top: 100px;
    }

    .fade-in {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- En-tête du panier -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Mon Panier</h1>
                    <p class="text-gray-600">
                        @if(count($cartItems) > 0)
                            {{ count($cartItems) }} article{{ count($cartItems) > 1 ? 's' : '' }} dans votre panier
                        @else
                            Votre panier est vide
                        @endif
                    </p>
                </div>

                @if(count($cartItems) > 0)
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($total, 0, ',', ' ') }} CFA</p>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <nav class="flex space-x-4 flex-wrap">
                    <a href="{{ route('products.index') }}"
                       class="flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Continuer mes achats
                    </a>

                    @if(count($cartItems) > 0)
                        <button id="clearAllBtn"
                                class="flex items-center text-red-600 hover:text-red-700 font-medium transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Vider le panier
                        </button>
                    @endif
                </nav>
            </div>
        </div>

        @if(count($cartItems) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Liste des articles -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $id => $item)
                        <div class="cart-item bg-white rounded-2xl shadow-lg p-6 fade-in"
                             data-product-id="{{ $id }}"
                             style="animation-delay: {{ $loop->index * 0.1 }}s">

                            <div class="flex items-center gap-6">
                                <!-- Image du produit -->
                                <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                    <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : asset('images/product-placeholder.jpg') }}"
                                         alt="{{ $item['product']->name }}"
                                         class="w-full h-full object-cover">
                                </div>

                                <!-- Informations du produit -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-gray-800 mb-2">
                                        <a href="{{ route('products.show', $item['product']->slug) }}"
                                           class="hover:text-blue-600 transition-colors">
                                            {{ $item['product']->name }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item['product']->description }}</p>

                                    <div class="flex items-center gap-4 flex-wrap">
                                        <span class="text-blue-600 font-bold text-lg">{{ number_format($item['product']->price, 0, ',', ' ') }} CFA</span>

                                        @if($item['product']->brand)
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                <i class="fas fa-tag mr-1"></i> {{ $item['product']->brand }}
                                            </span>
                                        @endif

                                        @if($item['product']->stock_quantity && $item['product']->stock_quantity <= 5)
                                            <span class="text-orange-500 text-xs bg-orange-50 px-2 py-1 rounded">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Plus que {{ $item['product']->stock_quantity }} en stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Contrôles de quantité et prix -->
                                <div class="flex flex-col items-end gap-4 flex-shrink-0">
                                    <!-- Prix total pour cet article -->
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Sous-total</p>
                                        <p class="font-bold text-xl text-green-600">{{ number_format($item['subtotal'], 0, ',', ' ') }} CFA</p>
                                    </div>

                                    <!-- Contrôles de quantité -->
                                    <div class="flex items-center border border-gray-200 rounded-lg">
                                        <button class="quantity-control p-3 hover:bg-gray-100 transition-colors"
                                                onclick="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})">
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>

                                        <input type="number"
                                               class="w-16 text-center border-0 outline-none font-semibold py-3"
                                               value="{{ $item['quantity'] }}"
                                               min="1"
                                               onchange="updateQuantity({{ $id }}, this.value)"
                                               id="qty-{{ $id }}">

                                        <button class="quantity-control p-3 hover:bg-gray-100 transition-colors"
                                                onclick="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>

                                    <!-- Bouton supprimer -->
                                    <button class="remove-btn p-3 rounded-lg border border-gray-200 hover:border-red-200 transition-all"
                                            onclick="removeItem({{ $id }})"
                                            title="Supprimer cet article">
                                        <i class="fas fa-trash text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Résumé du panier -->
                <div class="lg:col-span-1">
                    <div class="cart-summary bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Résumé de la commande</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sous-total ({{ count($cartItems) }} articles)</span>
                                <span class="font-semibold" id="cart-subtotal">{{ number_format($total, 0, ',', ' ') }} CFA</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Frais de livraison</span>
                                <span class="font-semibold text-green-600">Gratuits</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Taxes</span>
                                <span class="text-gray-600">Incluses</span>
                            </div>

                            <hr class="border-gray-200">

                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-blue-600" id="cart-total">{{ number_format($total, 0, ',', ' ') }} CFA</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-4">
                            <a href="{{ route('orders.create') }}"
                               class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                                <i class="fas fa-credit-card mr-3"></i>
                                Procéder au paiement
                            </a>

                            <button onclick="saveLater()"
                                    class="w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center">
                                <i class="fas fa-bookmark mr-2"></i>
                                Sauvegarder pour plus tard
                            </button>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="space-y-4 text-sm">
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-shield-alt mr-3 w-4"></i>
                                    <span>Paiement 100% sécurisé</span>
                                </div>
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-truck mr-3 w-4"></i>
                                    <span>Livraison gratuite</span>
                                </div>
                                <div class="flex items-center text-purple-600">
                                    <i class="fas fa-undo mr-3 w-4"></i>
                                    <span>Retour sous 30 jours</span>
                                </div>
                                <div class="flex items-center text-orange-600">
                                    <i class="fas fa-headset mr-3 w-4"></i>
                                    <span>Support client 24/7</span>
                                </div>
                            </div>
                        </div>

                        <!-- Code promo -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-800 mb-3">Code promo</h3>
                            <div class="flex gap-2">
                                <input type="text"
                                       id="promoCode"
                                       placeholder="Entrez votre code"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button onclick="applyPromo()"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                    Appliquer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Panier vide -->
            <div class="empty-cart rounded-2xl shadow-lg p-16 text-center">
                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
                </div>

                <h2 class="text-4xl font-bold text-gray-700 mb-6">Votre panier est vide</h2>
                <p class="text-gray-500 text-xl mb-8 max-w-md mx-auto">Découvrez nos produits exceptionnels et ajoutez-les à votre panier pour commencer vos achats.</p>

                <div class="space-y-6">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center bg-gradient-to-r from-blue-500 to-purple-600 text-white px-10 py-4 rounded-xl font-bold text-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        Découvrir nos produits
                    </a>

                    <div class="flex flex-col sm:flex-row gap-6 justify-center">
                        <a href="{{ url('/contact') }}"
                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold text-lg">
                            <i class="fas fa-headset mr-2"></i>
                            Besoin d'aide ?
                        </a>

                        <a href="{{ url('/services') }}"
                           class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold text-lg">
                            <i class="fas fa-tools mr-2"></i>
                            Nos services
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 opacity-0 invisible transition-all duration-300 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform scale-95 transition-transform duration-300">
        <div class="p-8">
            <div class="flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mx-auto mb-6">
                <i class="fas fa-trash text-red-600 text-3xl"></i>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 text-center mb-4">Confirmer l'action</h3>
            <p id="confirmMessage" class="text-gray-600 text-center mb-8">Êtes-vous sûr de vouloir effectuer cette action ?</p>

            <div class="flex space-x-4">
                <button id="cancelBtn" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button id="confirmBtn" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-semibold hover:bg-red-600 transition-colors">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    let pendingAction = null;

    // Gestion des modals
    function showModal(message, action) {
        confirmMessage.textContent = message;
        pendingAction = action;
        confirmModal.classList.remove('invisible', 'opacity-0');
        confirmModal.classList.add('opacity-100');
        confirmModal.querySelector('.bg-white').classList.remove('scale-95');
        confirmModal.querySelector('.bg-white').classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        confirmModal.classList.add('opacity-0');
        confirmModal.querySelector('.bg-white').classList.add('scale-95');
        confirmModal.querySelector('.bg-white').classList.remove('scale-100');

        setTimeout(() => {
            confirmModal.classList.add('invisible');
            document.body.style.overflow = '';
        }, 300);

        pendingAction = null;
    }

    // Event listeners pour les modals
    cancelBtn.addEventListener('click', hideModal);
    confirmBtn.addEventListener('click', function() {
        if (pendingAction) {
            pendingAction();
        }
        hideModal();
    });

    // Fermer le modal en cliquant à l'extérieur
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            hideModal();
        }
    });

    // Vider tout le panier
    document.getElementById('clearAllBtn')?.addEventListener('click', function() {
        showModal('Êtes-vous sûr de vouloir vider complètement votre panier ?', function() {
            clearAllCart();
        });
    });

    // Animation d'entrée des éléments
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';

        setTimeout(() => {
            item.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Fonctions globales
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeItem(productId);
        return;
    }

    fetch('{{ route('cart.update') }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Une erreur s\'est produite');
            document.getElementById(`qty-${productId}`).value = 1;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la mise à jour');
        document.getElementById(`qty-${productId}`).value = 1;
    });
}

function removeItem(productId) {
    fetch('{{ route('cart.remove') }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animation de suppression
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            if (item) {
                item.style.transform = 'translateX(-100%)';
                item.style.opacity = '0';
                setTimeout(() => {
                    location.reload();
                }, 300);
            } else {
                location.reload();
            }
        } else {
            alert(data.message || 'Une erreur s\'est produite');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la suppression');
    });
}

function clearAllCart() {
    fetch('{{ route('cart.clear') }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Une erreur s\'est produite');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors du vidage du panier');
    });
}

function saveLater() {
    alert('Fonctionnalité "Sauvegarder pour plus tard" à implémenter');
}

function applyPromo() {
    const promoCode = document.getElementById('promoCode').value;
    if (!promoCode.trim()) {
        alert('Veuillez entrer un code promo');
        return;
    }

    // À implémenter selon vos besoins
    alert('Fonctionnalité code promo à implémenter');
}
</script>
@endpush
