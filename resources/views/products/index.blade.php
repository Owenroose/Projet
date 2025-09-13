@extends('layouts.app')

@section('title', 'Produits - Nova Tech Bénin')
@section('description', 'Découvrez notre catalogue de matériel informatique de qualité : ordinateurs, périphériques, composants et accessoires tech au Bénin.')

@push('styles')
<style>
    /* Animations personnalisées */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slideInUp {
        animation: slideInUp 0.6s ease-out forwards;
    }

    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }

    .animate-slideInRight {
        animation: slideInRight 0.3s ease-out forwards;
    }

    /* Gradient background pour le hero */
    .products-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Effets de carte produit */
    .product-card {
        transition: all 0.3s ease;
        backface-visibility: hidden;
    }

    .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-image {
        transition: transform 0.4s ease;
        object-fit: cover;
    }

    /* Badge de stock */
    .stock-badge {
        backdrop-filter: blur(10px);
    }

    /* Filtre animé */
    .filter-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    /* Effet de loading pour les filtres */
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid #3b82f6;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Styles pour la notification de panier */
    .cart-notification {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        padding: 1rem 1.5rem;
        background-color: #059669;
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

    /* Icône panier flottant */
    .cart-icon {
        position: fixed;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        z-index: 1000;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: #ef4444;
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

    /* Sidebar du panier */
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

    /* Animation du bouton d'ajout au panier */
    .add-to-cart-btn {
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        transform: scale(1.05);
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

    /* Responsive */
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
<div class="products-hero py-20 text-white relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-10 left-10 w-4 h-4 bg-white/20 rounded-full animate-pulse"></div>
        <div class="absolute top-32 right-20 w-6 h-6 bg-white/10 rounded-full animate-bounce"></div>
        <div class="absolute bottom-20 left-32 w-3 h-3 bg-white/30 rounded-full animate-pulse"></div>
        <div class="absolute bottom-40 right-16 w-5 h-5 bg-white/15 rounded-full animate-bounce"></div>
    </div>

    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-slideInUp">
                Notre <span class="text-yellow-300">Catalogue</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90 animate-slideInUp" style="animation-delay: 0.2s;">
                Découvrez notre sélection de matériel informatique de qualité professionnelle
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slideInUp" style="animation-delay: 0.4s;">
                <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                    <span class="text-2xl font-bold">{{ $products->count() }}</span>
                    <span class="text-sm opacity-80 ml-2">Produits disponibles</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                    <span class="text-2xl font-bold">{{ $categories->count() }}</span>
                    <span class="text-sm opacity-80 ml-2">Catégories</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-lg sticky top-20 z-40 border-b border-gray-100">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6 items-center">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Rechercher un produit..."
                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="filter-btn px-6 py-2 bg-blue-500 text-white rounded-full font-semibold shadow-md hover:bg-blue-600 active"
                        data-category="all">
                    Tous les produits
                </button>
                @foreach($categories as $category)
                    <button class="filter-btn px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-semibold hover:bg-gray-200 shadow-md"
                            data-category="{{ $category->category }}">
                        {{ ucfirst($category->category) }}
                    </button>
                @endforeach
            </div>

            <div class="relative">
                <select id="sortSelect" class="appearance-none bg-white border border-gray-200 rounded-full px-6 py-3 pr-12 font-semibold focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="name">Nom A-Z</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                    <option value="newest">Plus récents</option>
                </select>
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cart-icon" id="cartToggle">
    <i class="fas fa-shopping-cart text-xl"></i>
    <div class="cart-count" id="cartCount">0</div>
</div>

<div class="cart-overlay" id="cartOverlay"></div>

<div class="cart-sidebar" id="cartSidebar">
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Mon Panier</h2>
            <button id="closeCart" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <p class="text-blue-100 mt-1" id="cartItemsCount">0 article(s)</p>
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
            <span class="text-2xl font-bold text-blue-600" id="cartTotal">0 CFA</span>
        </div>
        <div class="space-y-3">
            <button id="checkoutBtn" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
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

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div id="noResults" class="hidden text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-600 mb-4">Aucun produit trouvé</h3>
            <p class="text-gray-500">Essayez de modifier vos critères de recherche ou explorez nos autres catégories.</p>
        </div>

        <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($products as $index => $product)
                <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden product-item animate-slideInUp"
                     data-category="{{ $product->category }}"
                     data-name="{{ strtolower($product->name) }}"
                     data-price="{{ $product->price }}"
                     style="animation-delay: {{ $index * 0.1 }}s;">

                    <div class="relative overflow-hidden aspect-square">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/product-placeholder.jpg') }}"
                             alt="{{ $product->name }}"
                             class="product-image w-full h-full object-cover">

                        <div class="absolute top-4 right-4">
                            @if($product->in_stock)
                                <span class="stock-badge bg-green-500/90 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> En stock
                                </span>
                            @else
                                <span class="stock-badge bg-red-500/90 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-times-circle mr-1"></i> Rupture
                                </span>
                            @endif
                        </div>

                        <div class="absolute top-4 left-4">
                            <span class="bg-blue-500/90 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                {{ ucfirst($product->category) }}
                            </span>
                        </div>

                        <div class="absolute inset-0 bg-black/60 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="flex gap-3">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="bg-white text-gray-800 p-3 rounded-full hover:bg-blue-500 hover:text-white transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($product->in_stock)
                                    <button class="add-to-cart-btn bg-white text-gray-800 p-3 rounded-full hover:bg-green-500 hover:text-white transition-all duration-200 transform hover:scale-110 relative"
                                            onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->image }}', '{{ $product->slug }}')">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="text-gray-600 text-sm line-clamp-3">{{ $product->description }}</p>
                        </div>

                        @if($product->brand)
                            <div class="flex items-center mb-3">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    <i class="fas fa-tag mr-1"></i> {{ $product->brand }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($product->price, 0, ',', ' ') }} CFA</span>
                                @if($product->stock_quantity && $product->stock_quantity <= 5)
                                    <div class="text-xs text-orange-500 mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Plus que {{ $product->stock_quantity }} en stock
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                @if($product->in_stock)
                                    <button class="add-to-cart-btn bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-green-600 transition-all duration-200 transform hover:scale-105 relative"
                                            onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image }}', '{{ $product->slug }}')">
                                        <i class="fas fa-cart-plus mr-1"></i>
                                        Ajouter
                                    </button>
                                @else
                                    <span class="bg-gray-300 text-gray-500 px-4 py-2 rounded-full text-sm font-semibold cursor-not-allowed">
                                        Indisponible
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-4">Aucun produit disponible</h3>
                    <p class="text-gray-500 mb-6">Notre catalogue sera bientôt mis à jour avec de nouveaux produits.</p>
                    <a href="{{ url('/contact') }}" class="nova-btn nova-btn-primary">
                        <i class="fas fa-envelope mr-2"></i> Nous contacter
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Besoin d'aide pour choisir ?
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Notre équipe d'experts est là pour vous conseiller et vous aider à trouver le matériel parfait pour vos besoins.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/contact') }}" class="nova-btn nova-btn-primary bg-white text-blue-600 hover:bg-gray-100 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-headset mr-2"></i> Demander conseil
                </a>
                <a href="{{ url('/services') }}" class="nova-btn nova-btn-secondary border border-white text-white hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-tools mr-2"></i> Nos services
                </a>
            </div>
        </div>
    </div>
</section>

<div id="cart-notification" class="cart-notification">
    <span id="notification-message"></span>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productItems = document.querySelectorAll('.product-item');
    const productsGrid = document.getElementById('productsGrid');
    const noResults = document.getElementById('noResults');

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

    let currentCategory = 'all';
    let currentSort = 'name';
    let currentSearch = '';
    let cart = {};

    // Initialisation du panier
    loadCartFromStorage();

    // Event listeners pour le panier
    cartToggle.addEventListener('click', toggleCart);
    closeCart.addEventListener('click', closeCartSidebar);
    cartOverlay.addEventListener('click', closeCartSidebar);
    clearCartBtn.addEventListener('click', clearCart);

    // MODIFICATION ICI: Logique pour le bouton de paiement
    checkoutBtn.addEventListener('click', function() {
        if (Object.keys(cart).length === 0) {
            showNotification('Votre panier est vide. Ajoutez des produits pour continuer.', 'error');
            return;
        }

        // Ajout d'un effet de chargement pour le bouton
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Redirection...';
        checkoutBtn.disabled = true;
        checkoutBtn.classList.add('opacity-75', 'cursor-not-allowed');

        // Envoi d'une requête AJAX pour synchroniser le panier avec la session
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
            // Si la synchronisation réussit, rediriger vers la vue de commande
            window.location.href = '{{ route('orders.create') }}';
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification(error.message, 'error');
            // Réinitialiser le bouton en cas d'erreur
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            checkoutBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        });
    });

    // Filtrage par catégorie
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('bg-blue-500', 'text-white', 'active');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });

            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-blue-500', 'text-white', 'active');

            currentCategory = this.dataset.category;
            filterProducts();
        });
    });

    // Recherche
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.toLowerCase();
            filterProducts();
        }, 300);
    });

    // Tri
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        sortProducts();
    });

    // Fonctions de filtrage et tri
    function filterProducts() {
        let visibleCount = 0;

        productItems.forEach((item, index) => {
            const category = item.dataset.category;
            const name = item.dataset.name;

            const matchesCategory = currentCategory === 'all' || category === currentCategory;
            const matchesSearch = currentSearch === '' || name.includes(currentSearch);

            if (matchesCategory && matchesSearch) {
                item.style.display = 'block';
                item.style.animationDelay = `${visibleCount * 0.1}s`;
                item.classList.add('animate-fadeIn');
                visibleCount++;
            } else {
                item.style.display = 'none';
                item.classList.remove('animate-fadeIn');
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            productsGrid.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            productsGrid.classList.remove('hidden');
        }
    }

    function sortProducts() {
        const items = Array.from(productItems);

        items.sort((a, b) => {
            switch (currentSort) {
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'price_asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price_desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'newest':
                    return 0;
                default:
                    return 0;
            }
        });

        items.forEach((item, index) => {
            productsGrid.appendChild(item);
            item.style.animationDelay = `${index * 0.05}s`;
        });
    }

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

        // Mettre à jour le compteur
        cartCount.textContent = totalItems;
        cartCount.classList.toggle('show', totalItems > 0);

        // Mettre à jour le texte du nombre d'articles
        cartItemsCount.textContent = `${totalItems} article${totalItems > 1 ? 's' : ''}`;

        // Mettre à jour le total
        cartTotal.textContent = `${totalPrice.toLocaleString('fr-FR')} CFA`;

        // Afficher/masquer les éléments
        if (itemCount === 0) {
            emptyCart.style.display = 'block';
            cartFooter.style.display = 'none';
        } else {
            emptyCart.style.display = 'none';
            cartFooter.style.display = 'block';
        }

        // Mettre à jour la liste des articles
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
                        <p class="text-blue-600 font-bold text-sm">${item.price.toLocaleString('fr-FR')} CFA</p>
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

        cartHTML += '</div>';
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

    // Fonction globale pour ajouter au panier
    window.addToCart = function(productId, name, price, image, slug) {
        const button = event.target.closest('.add-to-cart-btn');

        // Animation de chargement
        button.classList.add('loading');
        button.disabled = true;

        // Simuler un délai pour l'effet visuel
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

            // Retirer l'animation de chargement
            button.classList.remove('loading');
            button.disabled = false;

            // Animation de succès
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = '';
            }, 150);

        }, 500);
    };

    // Fonction pour mettre à jour la quantité
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

    // Fonction pour supprimer un article
    window.removeFromCart = function(productId) {
        if (cart[productId]) {
            const productName = cart[productId].name;
            delete cart[productId];
            saveCartToStorage();
            updateCartDisplay();
            showNotification(`${productName} retiré du panier`, 'info');
        }
    };

    // Fonction d'affichage de la notification
    function showNotification(message, type = 'success') {
        const notification = document.getElementById('cart-notification');
        const notificationMessage = document.getElementById('notification-message');

        notificationMessage.textContent = message;

        // Couleurs selon le type
        switch(type) {
            case 'success':
                notification.style.backgroundColor = '#059669';
                break;
            case 'error':
                notification.style.backgroundColor = '#dc2626';
                break;
            case 'info':
                notification.style.backgroundColor = '#2563eb';
                break;
            default:
                notification.style.backgroundColor = '#059669';
        }

        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les cartes qui ne sont pas encore visibles
    productItems.forEach(item => {
        if (item.getBoundingClientRect().top > window.innerHeight) {
            item.style.opacity = '0';
            item.style.transform = 'translateY(40px)';
            item.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(item);
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
        if (window.innerWidth >= 640 && cartSidebar.classList.contains('open')) {
            // Ajuster la largeur du sidebar si nécessaire
        }
    });
});
</script>
@endpush
