@extends('layouts.app')

@section('title', 'Accueil - Nova Tech Bénin')
@section('description', 'Nova Tech Bénin offre des solutions de développement web et mobile sur mesure ainsi que la vente de matériel informatique de qualité.')

@section('content')

<!-- Section du Hero (Slider) -->
<section class="relative bg-gray-900 overflow-hidden text-center text-white py-24 md:py-32">
    <div class="absolute inset-0">
        <!-- Overlay pour améliorer la lisibilité du texte -->
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <img src="{{ asset('images/banner2.jpg') }}" alt="Nova Tech Solutions" class="w-full h-full object-cover">
    </div>
    <div class="relative container mx-auto px-4 z-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4">
                Votre
                <br>
                <strong class="block font-extrabold text-white">Partenaire</strong>
                <strong class="block text-yellow-400">Technologique</strong>
            </h1>
            <p class="text-base md:text-lg mb-8 max-w-2xl mx-auto">
                Des solutions digitales innovantes et du matériel informatique de qualité pour propulser votre entreprise vers l'avenir.
            </p>
            <a href="{{ url('/services') }}" class="nova-btn nova-btn-primary">
                Découvrir nos services
            </a>
        </div>
    </div>
</section>

<!-- Section À propos (Bienvenue) -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Contenu de la section À propos -->
            <div class="order-2 md:order-1">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Bienvenue chez Nova Tech</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Nous sommes une entreprise technologique passionnée, basée au Bénin, dédiée à la conception de solutions digitales sur mesure et à la fourniture de matériel informatique de pointe. Notre mission est de simplifier la technologie pour les entreprises et les particuliers, en offrant un service de qualité supérieure et un soutien indéfectible.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Chez Nova Tech, nous croyons que l'innovation est la clé du succès. Nous travaillons en étroite collaboration avec nos clients pour transformer leurs idées en réalités numériques, en utilisant les dernières technologies pour garantir des résultats à la fois efficaces et durables.
                </p>
                <a href="{{ url('/about') }}" class="nova-btn nova-btn-primary mt-8">
                    En savoir plus
                </a>
            </div>
            <!-- Image de la section À propos -->
            <div class="order-1 md:order-2">
                <img src="{{ asset('images/3.jpg') }}" alt="Équipe Nova Tech" class="rounded-lg shadow-xl w-full">
            </div>
        </div>
    </div>
</section>

<!-- Section Services -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Nos Services</h2>
        <p class="text-lg text-gray-600 mb-12 max-w-2xl mx-auto">
            Découvrez nos solutions sur mesure conçues pour propulser votre entreprise dans l'ère du numérique.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Carte Service 1 -->
            <div class="bg-white rounded-lg shadow-lg p-8 transition-transform transform hover:scale-105">
                <div class="text-blue-600 mb-4 text-4xl">
                    <i class="fas fa-desktop"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Développement Web</h3>
                <p class="text-gray-600 text-sm">
                    Création de sites web et d'applications modernes, réactives et optimisées.
                </p>
            </div>
            <!-- Carte Service 2 -->
            <div class="bg-white rounded-lg shadow-lg p-8 transition-transform transform hover:scale-105">
                <div class="text-blue-600 mb-4 text-4xl">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Développement Mobile</h3>
                <p class="text-gray-600 text-sm">
                    Applications mobiles iOS et Android performantes et intuitives.
                </p>
            </div>
            <!-- Carte Service 3 -->
            <div class="bg-white rounded-lg shadow-lg p-8 transition-transform transform hover:scale-105">
                <div class="text-blue-600 mb-4 text-4xl">
                    <i class="fas fa-laptop-house"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Vente de Matériel</h3>
                <p class="text-gray-600 text-sm">
                    Fourniture de matériel informatique de qualité (ordinateurs, serveurs, etc.).
                </p>
            </div>
        </div>
        <a href="{{ url('/services') }}" class="nova-btn nova-btn-primary mt-12">Voir tous les services</a>
    </div>
</section>

<!-- Section CTA (Appel à l'action) -->
<section class="bg-blue-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à démarrer votre projet ?</h2>
        <p class="text-lg mb-8 max-w-2xl mx-auto">
            Contactez notre équipe pour une consultation gratuite et sans engagement.
        </p>
        <a href="{{ url('/contact') }}" class="nova-btn bg-white text-blue-600 hover:bg-gray-100">
            Contactez-nous
        </a>
    </div>
</section>

<!-- Section Témoignages -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Ce que nos clients disent</h2>
        <p class="text-lg text-gray-600 mb-12 max-w-2xl mx-auto">
            Des partenariats solides fondés sur la confiance et l'excellence.
        </p>
        <div class="relative w-full max-w-4xl mx-auto">
            <div id="testimonial-slider" class="flex overflow-x-hidden scrollbar-none snap-x snap-mandatory">
                <!-- Témoignage 1 -->
                <div class="flex-shrink-0 w-full snap-center p-4">
                    <div class="bg-gray-100 rounded-lg p-8 shadow-inner">
                        <p class="italic text-gray-700 mb-4">
                            "Nova Tech a dépassé toutes nos attentes. Leur professionnalisme et leur expertise ont été cruciaux pour le succès de notre projet. Nous les recommandons vivement."
                        </p>
                        <span class="text-blue-600 font-semibold">- Jean Dupont, CEO</span>
                    </div>
                </div>
                <!-- Témoignage 2 -->
                <div class="flex-shrink-0 w-full snap-center p-4">
                    <div class="bg-gray-100 rounded-lg p-8 shadow-inner">
                        <p class="italic text-gray-700 mb-4">
                            "Le service client est exceptionnel et la qualité du matériel fourni est irréprochable. Un vrai plaisir de travailler avec eux."
                        </p>
                        <span class="text-blue-600 font-semibold">- Marie Lefebvre, Gérante</span>
                    </div>
                </div>
            </div>
            <!-- Boutons de navigation -->
            <button id="prev-btn" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100">
                <i class="fas fa-chevron-left text-blue-600"></i>
            </button>
            <button id="next-btn" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100">
                <i class="fas fa-chevron-right text-blue-600"></i>
            </button>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Slider de témoignages en Vanilla JS
        const slider = document.getElementById('testimonial-slider');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        if (slider && prevBtn && nextBtn) {
            const scrollAmount = slider.clientWidth;

            prevBtn.addEventListener('click', () => {
                slider.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });

            nextBtn.addEventListener('click', () => {
                slider.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
@endpush
