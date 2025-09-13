@extends('layouts.app')

@section('title', 'Commande confirmée - Nova Tech Bénin')
@section('description', 'Votre commande a été confirmée avec succès. Merci pour votre confiance.')

@push('styles')
<style>
    .success-animation {
        animation: successPulse 1.5s ease-in-out;
    }

    @keyframes successPulse {
        0% { transform: scale(0.8); opacity: 0; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }

    .order-card {
        transition: all 0.3s ease;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .timeline-step {
        transition: all 0.5s ease;
    }

    .step-active {
        background: #10b981;
        color: white;
        transform: scale(1.1);
    }

    .step-upcoming {
        background: #e5e7eb;
        color: #6b7280;
    }

    .step-completed {
        background: #10b981;
        color: white;
    }

    .step-current {
        background: #3b82f6;
        color: white;
        transform: scale(1.1);
    }

    .step-cancelled {
        background: #ef4444;
        color: white;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #3b82f6;
        animation: confetti-fall 3s linear infinite;
    }

    @keyframes confetti-fall {
        0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }

    .confetti:nth-child(1) { left: 10%; background: #ef4444; animation-delay: 0s; }
    .confetti:nth-child(2) { left: 20%; background: #f59e0b; animation-delay: 0.2s; }
    .confetti:nth-child(3) { left: 30%; background: #10b981; animation-delay: 0.4s; }
    .confetti:nth-child(4) { left: 40%; background: #3b82f6; animation-delay: 0.6s; }
    .confetti:nth-child(5) { left: 50%; background: #8b5cf6; animation-delay: 0.8s; }
    .confetti:nth-child(6) { left: 60%; background: #ec4899; animation-delay: 1s; }
    .confetti:nth-child(7) { left: 70%; background: #06b6d4; animation-delay: 1.2s; }
    .confetti:nth-child(8) { left: 80%; background: #84cc16; animation-delay: 1.4s; }
    .confetti:nth-child(9) { left: 90%; background: #f97316; animation-delay: 1.6s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 relative overflow-hidden">
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>

    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <div class="success-animation inline-flex items-center justify-center w-24 h-24 bg-green-500 text-white rounded-full text-4xl mb-6 shadow-lg">
                <i class="fas fa-check"></i>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Commande confirmée !
            </h1>

            <p class="text-xl text-gray-600 mb-2">
                Merci pour votre confiance, votre commande a été enregistrée avec succès.
            </p>

            <p class="text-gray-500">
                Vous recevrez un email de confirmation à {{ $order->email }}
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="order-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Détails de votre commande</h2>

                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusText = [
                                    'pending' => 'En attente',
                                    'processing' => 'En cours',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'cancelled' => 'Annulée',
                                ];
                                $statusIcon = [
                                    'pending' => 'fas fa-clock',
                                    'processing' => 'fas fa-sync',
                                    'shipped' => 'fas fa-truck',
                                    'delivered' => 'fas fa-check-circle',
                                    'cancelled' => 'fas fa-times-circle',
                                ];
                            @endphp
                            <div class="{{ $statusClasses[$order->status] }} px-4 py-2 rounded-full text-sm font-semibold">
                                <i class="{{ $statusIcon[$order->status] }} mr-1"></i>
                                {{ $statusText[$order->status] }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Numéro de commande</label>
                                <p class="text-lg font-mono bg-gray-50 p-3 rounded-lg">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Date de commande</label>
                                <p class="text-lg bg-gray-50 p-3 rounded-lg">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de livraison</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Nom complet</label>
                                    <p class="font-semibold">{{ $order->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Téléphone</label>
                                    <p class="font-semibold">{{ $order->phone }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm text-gray-600 mb-1">Adresse de livraison</label>
                                    <p class="font-semibold">{{ $order->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Produit{{ $orderItems->count() > 1 ? 's' : '' }} commandé{{ $orderItems->count() > 1 ? 's' : '' }} ({{ $orderItems->count() }} article{{ $orderItems->count() > 1 ? 's' : '' }})
                        </h2>

                        <div class="space-y-4">
                            @foreach($orderItems as $item)
                                <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-xl">
                                    <div class="w-16 h-16 bg-white rounded-lg overflow-hidden flex-shrink-0">
                                        <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : asset('images/product-placeholder.jpg') }}"
                                             alt="{{ $item['product']->name }}"
                                             class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-lg">{{ $item['product']->name }}</h4>
                                        @if($item['product']->brand)
                                            <p class="text-blue-600 text-sm">{{ $item['product']->brand }}</p>
                                        @endif
                                        <p class="text-gray-600 text-sm">{{ number_format($item['product']->price, 0, ',', ' ') }} CFA × {{ $item['quantity'] }}</p>
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <div class="text-sm text-gray-500 mb-1">Quantité: {{ $item['quantity'] }}</div>
                                        <div class="text-xl font-bold text-blue-600">{{ number_format($item['subtotal'], 0, ',', ' ') }} CFA</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-900">Total de la commande</span>
                                <span class="text-3xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', ' ') }} CFA</span>
                            </div>
                        </div>
                    </div>

                    <div class="order-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Suivi de votre commande</h2>

                        @php
                            $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                            $currentStatusIndex = array_search($order->status, $statuses);
                        @endphp

                        <div class="relative">
                            <div class="absolute left-8 top-8 bottom-8 w-0.5 bg-gray-200"></div>

                            <div class="space-y-8">
                                <div class="timeline-step flex items-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg
                                        @if($currentStatusIndex >= 0) step-completed @else step-upcoming @endif">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="text-lg font-semibold text-gray-900">Commande confirmée</h3>
                                        <p class="text-gray-600">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                                        <p class="text-sm text-green-600 font-medium">✓ Terminé</p>
                                    </div>
                                </div>

                                <div class="timeline-step flex items-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                                        @if($currentStatusIndex >= 1) step-completed @else step-upcoming @endif">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="text-lg font-semibold text-gray-700">Préparation</h3>
                                        <p class="text-gray-500">Nous préparons votre commande</p>
                                        <p class="text-sm text-gray-400">@if($currentStatusIndex >= 1) ✓ Terminé @else En attente @endif</p>
                                    </div>
                                </div>

                                <div class="timeline-step flex items-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                                        @if($currentStatusIndex >= 2) step-completed @else step-upcoming @endif">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="text-lg font-semibold text-gray-700">En livraison</h3>
                                        <p class="text-gray-500">Votre commande est en route</p>
                                        <p class="text-sm text-gray-400">@if($currentStatusIndex >= 2) ✓ Terminé @else En attente @endif</p>
                                    </div>
                                </div>

                                <div class="timeline-step flex items-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                                        @if($currentStatusIndex >= 3) step-completed @else step-upcoming @endif">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="ml-6">
                                        <h3 class="text-lg font-semibold text-gray-700">Livraison</h3>
                                        <p class="text-gray-500">Réception de votre commande</p>
                                        <p class="text-sm text-gray-400">@if($currentStatusIndex >= 3) ✓ Terminé @else En attente @endif</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl p-6">
                        <div class="flex items-center text-yellow-800 mb-4">
                            <i class="fas fa-money-bill-wave mr-3 text-2xl"></i>
                            <span class="font-bold text-lg">Paiement à la livraison</span>
                        </div>
                        <p class="text-yellow-700 text-sm leading-relaxed mb-4">
                            Vous paierez en espèces lors de la réception de votre commande.
                        </p>
                        <div class="bg-yellow-100 rounded-lg p-3">
                            <div class="text-yellow-800 text-sm font-semibold mb-1">Montant à préparer:</div>
                            <div class="text-2xl font-bold text-yellow-900">
                                {{ number_format($totalAmount, 0, ',', ' ') }} CFA
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                        <h3 class="font-bold text-blue-800 mb-4 flex items-center text-lg">
                            <i class="fas fa-headset mr-3"></i>
                            Support client
                        </h3>
                        <p class="text-blue-700 text-sm mb-4">
                            Une question sur votre commande ? Notre équipe est là pour vous aider.
                        </p>
                        <div class="space-y-3">
                            <a href="tel:+22912345678"
                               class="flex items-center bg-blue-100 hover:bg-blue-200 text-blue-800 p-3 rounded-lg transition-colors">
                                <i class="fas fa-phone mr-3"></i>
                                <div>
                                    <div class="font-semibold">+229 12 34 56 78</div>
                                    <div class="text-xs">Lun-Sam 8h-18h</div>
                                </div>
                            </a>

                            <a href="mailto:contact@novatechbenin.com"
                               class="flex items-center bg-blue-100 hover:bg-blue-200 text-blue-800 p-3 rounded-lg transition-colors">
                                <i class="fas fa-envelope mr-3"></i>
                                <div>
                                    <div class="font-semibold">contact@novatechbenin.com</div>
                                    <div class="text-xs">Réponse sous 24h</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6">
                        <h3 class="font-bold text-green-800 mb-4 flex items-center text-lg">
                            <i class="fas fa-clock mr-3"></i>
                            Délais de livraison
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-map-marker-alt mr-3 w-4"></i>
                                <div>
                                    <div class="font-semibold">Cotonou</div>
                                    <div class="text-sm">24-48h ouvrées</div>
                                </div>
                            </div>
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-city mr-3 w-4"></i>
                                <div>
                                    <div class="font-semibold">Autres villes</div>
                                    <div class="text-sm">2-5 jours ouvrés</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Actions rapides</h3>
                        <div class="space-y-3">
                            <a href="{{ route('products.index') }}"
                               class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-xl font-semibold transition-colors flex items-center justify-center">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Continuer mes achats
                            </a>

                            <a href="{{ route('orders.invoice', $order->id) }}"
                                target="_blank"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-xl font-semibold transition-colors flex items-center justify-center">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Voir la facture
                            </a>

                            <button onclick="shareOrder()"
                                    class="w-full bg-green-100 hover:bg-green-200 text-green-700 py-3 px-4 rounded-xl font-semibold transition-colors flex items-center justify-center">
                                <i class="fas fa-share mr-2"></i>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Vous pourriez aussi aimer</h2>
                <p class="text-gray-600">Découvrez d'autres produits qui pourraient vous intéresser</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center justify-center text-gray-400">
                    <i class="fas fa-heart text-4xl mr-4"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-600">Recommandations personnalisées</h3>
                        <p class="text-gray-500">Basées sur votre commande actuelle</p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-eye mr-2"></i>
                        Voir nos recommandations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation séquentielle des éléments
    const orderCards = document.querySelectorAll('.order-card');
    orderCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 300 + 500);
    });

    // Animation des éléments de la timeline
    const timelineSteps = document.querySelectorAll('.timeline-step');
    timelineSteps.forEach((step, index) => {
        step.style.opacity = '0';
        step.style.transform = 'translateX(-30px)';

        setTimeout(() => {
            step.style.transition = 'all 0.5s ease-out';
            step.style.opacity = '1';
            step.style.transform = 'translateX(0)';
        }, index * 200 + 1000);
    });

    // Arrêter les confettis après 5 secondes
    setTimeout(() => {
        const confettis = document.querySelectorAll('.confetti');
        confettis.forEach(confetti => {
            confetti.style.animation = 'none';
            confetti.style.display = 'none';
        });
    }, 5000);
});

// Fonction de partage de la commande
function shareOrder() {
    const orderNumber = '#{{ str_pad($order->id, 6, "0", STR_PAD_LEFT) }}';
    const text = `Ma commande ${orderNumber} chez Nova Tech Bénin a été confirmée ! 🎉`;

    if (navigator.share) {
        navigator.share({
            title: 'Commande Nova Tech Bénin',
            text: text,
            url: window.location.href
        });
    } else {
        // Fallback pour les navigateurs qui ne supportent pas Web Share API
        const textArea = document.createElement('textarea');
        textArea.value = text + ' ' + window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        // Notification temporaire
        const notification = document.createElement('div');
        notification.textContent = 'Lien copié dans le presse-papier !';
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        document.body.appendChild(notification);

        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }
}

// Impression optimisée
window.addEventListener('beforeprint', function() {
    // Cacher les confettis lors de l'impression
    const confettis = document.querySelectorAll('.confetti');
    confettis.forEach(confetti => {
        confetti.style.display = 'none';
    });
});
</script>
@endpush
