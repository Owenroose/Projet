@extends('layouts.app')

@section('title', $product->name . ' - Nova Tech Bénin')
@section('description', 'Découvrez ' . $product->name . ' : ' . Str::limit($product->description, 150) . ' Disponible chez Nova Tech Bénin.')

@push('styles')
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .prose {
        max-width: none;
    }

    .prose p {
        margin-bottom: 1rem;
    }

    /* Styles pour le panier (réplique du index.blade.php) */
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .cart-notification {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        padding: 1rem 1.5rem;
        background-color: #10B981; /* Green-500 */
        color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        z-index: 100;
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        transform: translateY(100%);
        opacity: 0;
    }

    .cart-notification.show {
        transform: translateY(0);
        opacity: 1;
    }

    .cart-icon {
        position: fixed;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        z-index: 1000;
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%); /* Indigo-600 to Blue-500 */
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .cart-icon:hover {
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    .cart-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #EF4444; /* Red-500 */
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        transform: scale(0);
        transition: transform 0.3s ease;
    }

    .cart-count.show {
        transform: scale(1);
    }

    .cart-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        width: 400px;
        background: white;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        z-index: 1001;
        overflow-y: auto;
    }

    .cart-sidebar.open {
        transform: translateX(0);
    }

    .cart-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .cart-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .add-to-cart-btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .add-to-cart-btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        transform: translate(-50%, -50%);
    }

    @media (max-width: 640px) {
        .cart-sidebar {
            width: 100%;
        }

        .cart-icon {
            right: 15px;
            width: 50px;
            height: 50px;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">
                    <i class="fas fa-home mr-1"></i>Accueil
                </a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Produits</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-gray-900 font-medium">{{ $product->name }}</span>
            </div>
        </div>
    </nav>

    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-12 animate-fadeIn">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <div class="relative bg-gray-100 p-8 flex items-center justify-center">
                        <img id="mainImage"
                             src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-contain rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">

                        @if($product->in_stock)
                            <div class="absolute top-8 left-8">
                                <span class="bg-emerald-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-md">
                                    <i class="fas fa-check mr-1"></i>Disponible
                                </span>
                            </div>
                        @else
                            <div class="absolute top-8 left-8">
                                <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-md">
                                    <i class="fas fa-exclamation mr-1"></i>Rupture
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-8 lg:p-12 space-y-8">
                        <div class="space-y-4">
                            @if($product->brand)
                                <div class="flex items-center text-indigo-600 font-semibold">
                                    <i class="fas fa-tag mr-2"></i>
                                    <span class="text-lg">{{ $product->brand }}</span>
                                </div>
                            @endif

                            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                                {{ $product->name }}
                            </h1>
                        </div>

                        <div class="flex items-center space-x-6">
                            <span class="text-4xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-shield-alt mr-2 text-indigo-400"></i>
                                <span class="text-gray-700">Garantie incluse</span>
                            </div>
                        </div>

                        <div class="prose max-w-none">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Description</h3>
                            <p class="text-gray-700 leading-relaxed text-lg">{{ $product->description }}</p>
                        </div>

                        <div class="space-y-4">
                            @if($product->in_stock)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <button class="add-to-cart-btn w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center text-lg relative"
                                            onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image }}', '{{ $product->slug }}')">
                                        <i class="fas fa-cart-plus mr-3"></i>
                                        Ajouter au panier
                                    </button>

                                    <a href="{{ route('orders.create', $product->slug) }}"
                                       class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center text-lg">
                                        <i class="fas fa-credit-card mr-3"></i>
                                        Commander maintenant
                                    </a>
                                </div>
                            @else
                                <button disabled
                                        class="w-full bg-gray-400 text-white font-semibold py-4 px-8 rounded-xl cursor-not-allowed flex items-center justify-center text-lg">
                                    <i class="fas fa-ban mr-3"></i>
                                    Produit indisponible
                                </button>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <a href="tel:+22912345678"
                                   class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                                    <i class="fas fa-phone mr-2"></i>
                                    Appeler
                                </a>
                                <a href="https://wa.me/22912345678"
                                   class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-2"></i>
                                    WhatsApp
                                </a>
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-shipping-fast text-indigo-600 text-2xl"></i>
                                    <span class="text-sm text-gray-600">Livraison rapide</span>
                                </div>
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-shield-alt text-emerald-600 text-2xl"></i>
                                    <span class="text-sm text-gray-600">Garantie</span>
                                </div>
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-headset text-fuchsia-600 text-2xl"></i>
                                    <span class="text-sm text-gray-600">Support 24/7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($product->specifications)
            <div class="bg-white rounded-3xl shadow-2xl p-8 mb-12 animate-fadeIn">
                <div class="flex items-center mb-8">
                    <div class="w-1 h-8 bg-indigo-600 rounded-full mr-4"></div>
                    <h2 class="text-3xl font-bold text-gray-900">Spécifications techniques</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($product->specifications_array as $key => $value)
                    <div class="bg-gradient-to-r from-gray-50 to-white p-6 rounded-xl border border-gray-100 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <span class="font-semibold text-indigo-700 text-lg">{{ $key }}</span>
                            <span class="text-gray-800 font-medium ml-4 text-right">{{ $value }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($relatedProducts->count() > 0)
            <div class="bg-white rounded-3xl shadow-2xl p-8 animate-fadeIn">
                <div class="flex items-center justify-center mb-12">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Produits similaires</h2>
                        <div class="w-20 h-1 bg-indigo-600 rounded-full mx-auto"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($relatedProducts as $relatedProduct)
                    <a href="{{ route('products.show', $relatedProduct->slug) }}"
                       class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-indigo-200">
                        <div class="relative overflow-hidden">
                            <img src="{{ !empty($relatedProduct->image) ? asset('storage/' . $relatedProduct->image) : asset('images/product-placeholder.jpg') }}"
                                 alt="{{ $relatedProduct->name }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors duration-200">
                                {{ Str::limit($relatedProduct->name, 50) }}
                            </h3>
                            <span class="text-indigo-600 font-bold text-xl">{{ $relatedProduct->formatted_price }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

<div class="cart-icon" id="cartToggle">
    <i class="fas fa-shopping-cart text-xl"></i>
    <div class="cart-count" id="cartCount">0</div>
</div>

<div class="cart-overlay" id="cartOverlay"></div>

<div class="cart-sidebar" id="cartSidebar">
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-blue-500 text-white">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Mon Panier</h2>
            <button id="closeCart" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <p class="text-indigo-100 mt-1" id="cartItemsCount">0 article(s)</p>
    </div>

    <div class="flex-1 overflow-y-auto">
        <div id="cartItems" class="p-6">
            <div id="emptyCart" class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Votre panier est vide</h3>
                <p class="text-gray-500 text-sm">Ajoutez des produits pour commencer vos achats</p>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 p-6 bg-gray-50" id="cartFooter">
        <div class="flex items-center justify-between mb-4">
            <span class="text-lg font-semibold">Total:</span>
            <span class="text-2xl font-bold text-indigo-600" id="cartTotal">0 CFA</span>
        </div>
        <div class="space-y-3">
            <button id="checkoutBtn" class="w-full bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-blue-600 transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-credit-card mr-2"></i>
                Procéder au paiement
            </button>
            <button id="clearCart" class="w-full bg-red-500 text-white py-2 rounded-lg font-semibold hover:bg-red-600 transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Vider le panier
            </button>
        </div>
    </div>
</div>

<div id="cart-notification" class="cart-notification">
    <span id="notification-message"></span>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements du panier
        const cartToggle = document.getElementById('cartToggle');
        const cartSidebar = document.getElementById('cartSidebar');
        const cartOverlay = document.getElementById('cartOverlay');
        const closeCart = document.getElementById('closeCart');
        const clearCartBtn = document.getElementById('clearCart');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const cartCount = document.getElementById('cartCount');
        const cartItemsCount = document.getElementById('cartItemsCount');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const emptyCart = document.getElementById('emptyCart');
        const cartFooter = document.getElementById('cartFooter');

        let cart = {};

        // Initialisation du panier
        loadCartFromStorage();

        // Event listeners pour le panier
        cartToggle.addEventListener('click', toggleCart);
        closeCart.addEventListener('click', closeCartSidebar);
        cartOverlay.addEventListener('click', closeCartSidebar);
        clearCartBtn.addEventListener('click', clearCart);

        // Logique pour le bouton de paiement
        checkoutBtn.addEventListener('click', function() {
            if (Object.keys(cart).length === 0) {
                showNotification('Votre panier est vide. Ajoutez des produits pour continuer.', 'error');
                return;
            }

            const originalText = checkoutBtn.innerHTML;
            checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Redirection...';
            checkoutBtn.disabled = true;
            checkoutBtn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('/cart/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cart: cart })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Erreur de synchronisation du panier.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    window.location.href = '{{ route('orders.create') }}';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification(error.message, 'error');
                    checkoutBtn.innerHTML = originalText;
                    checkoutBtn.disabled = false;
                    checkoutBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                });
        });

        // Fonctions du panier
        function toggleCart() {
            cartSidebar.classList.toggle('open');
            cartOverlay.classList.toggle('show');
            document.body.style.overflow = cartSidebar.classList.contains('open') ? 'hidden' : '';
        }

        function closeCartSidebar() {
            cartSidebar.classList.remove('open');
            cartOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function loadCartFromStorage() {
            const savedCart = localStorage.getItem('nova_cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCartDisplay();
            }
        }

        function saveCartToStorage() {
            localStorage.setItem('nova_cart', JSON.stringify(cart));
        }

        function updateCartDisplay() {
            const itemCount = Object.keys(cart).length;
            const totalItems = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = Object.values(cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);

            cartCount.textContent = totalItems;
            cartCount.classList.toggle('show', totalItems > 0);

            cartItemsCount.textContent = `${totalItems} article${totalItems > 1 ? 's' : ''}`;
            cartTotal.textContent = `${totalPrice.toLocaleString('fr-FR')} CFA`;

            if (itemCount === 0) {
                emptyCart.style.display = 'block';
                cartFooter.style.display = 'none';
            } else {
                emptyCart.style.display = 'none';
                cartFooter.style.display = 'block';
            }

            updateCartItems();
        }

        function updateCartItems() {
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCartDiv = document.getElementById('emptyCart');

            if (Object.keys(cart).length === 0) {
                cartItemsContainer.innerHTML = `
                <div id="emptyCart" class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Votre panier est vide</h3>
                    <p class="text-gray-500 text-sm">Ajoutez des produits pour commencer vos achats</p>
                </div>
            `;
                return;
            }

            let cartHTML = '<div class="space-y-4">';

            Object.entries(cart).forEach(([productId, item]) => {
                cartHTML += `
                <div class="flex items-center gap-4 p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="${item.image ? '/storage/' + item.image : '/images/product-placeholder.jpg'}"
                             alt="${item.name}"
                             class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 text-sm line-clamp-2">${item.name}</h4>
                        <p class="text-indigo-600 font-bold text-sm">${item.price.toLocaleString('fr-FR')} CFA</p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
                                onclick="updateQuantity(${productId}, ${item.quantity - 1})">
                            <i class="fas fa-minus text-xs"></i>
                        </button>

                        <span class="w-8 text-center font-semibold text-sm">${item.quantity}</span>

                        <button class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
                                onclick="updateQuantity(${productId}, ${item.quantity + 1})">
                            <i class="fas fa-plus text-xs"></i>
                        </button>

                        <button class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center transition-colors ml-2"
                                onclick="removeFromCart(${productId})">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            `;
            });

            cartItemsContainer.innerHTML = cartHTML;
        }

        function clearCart() {
            if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                cart = {};
                saveCartToStorage();
                updateCartDisplay();
                showNotification('Panier vidé avec succès', 'success');
            }
        }

        window.addToCart = function(productId, name, price, image, slug) {
            const button = event.target.closest('.add-to-cart-btn');
            button.classList.add('loading');
            button.disabled = true;

            setTimeout(() => {
                if (cart[productId]) {
                    cart[productId].quantity += 1;
                } else {
                    cart[productId] = {
                        id: productId,
                        name: name,
                        price: price,
                        image: image,
                        slug: slug,
                        quantity: 1
                    };
                }

                saveCartToStorage();
                updateCartDisplay();
                showNotification(`${name} ajouté au panier !`, 'success');

                button.classList.remove('loading');
                button.disabled = false;
                button.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    button.style.transform = '';
                }, 150);
            }, 500);
        };

        window.updateQuantity = function(productId, newQuantity) {
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }

            if (cart[productId]) {
                cart[productId].quantity = newQuantity;
                saveCartToStorage();
                updateCartDisplay();
            }
        };

        window.removeFromCart = function(productId) {
            if (cart[productId]) {
                const productName = cart[productId].name;
                delete cart[productId];
                saveCartToStorage();
                updateCartDisplay();
                showNotification(`${productName} retiré du panier`, 'info');
            }
        };

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('cart-notification');
            const notificationMessage = document.getElementById('notification-message');

            notificationMessage.textContent = message;

            switch(type) {
                case 'success':
                    notification.style.backgroundColor = '#10B981'; /* Green-500 */
                    break;
                case 'error':
                    notification.style.backgroundColor = '#EF4444'; /* Red-500 */
                    break;
                case 'info':
                    notification.style.backgroundColor = '#3B82F6'; /* Blue-500 */
                    break;
                default:
                    notification.style.backgroundColor = '#10B981';
            }

            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-fadeIn').forEach((el) => {
            if (el.getBoundingClientRect().top > window.innerHeight) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(40px)';
                el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(el);
            }
        });

        // Fermer le panier avec Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && cartSidebar.classList.contains('open')) {
                closeCartSidebar();
            }
        });

        // Gestion du redimensionnement de fenêtre
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 640 && cartSidebar.classList.contains('open')) {}
        });
    });
</script>
@endpush
