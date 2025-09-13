@extends('layouts.app')

@section('title', 'À propos - Nova Tech Bénin')
@section('description', 'Découvrez Nova Tech Bénin, votre partenaire en développement web/mobile et fournisseur de matériel informatique de qualité au Bénin.')

@push('styles')
<style>
    /* Animations personnalisées */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .animate-scaleIn {
        animation: scaleIn 0.5s ease-out forwards;
    }

    /* Gradient animé pour le hero */
    .gradient-bg {
        background: linear-gradient(-45deg, #3b82f6, #1e40af, #059669, #10b981);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Effet de lueur pour les cartes */
    .glow-card:hover {
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
    }

    /* Indicateur de progression pour les stats */
    .stat-progress {
        width: 0%;
        transition: width 2s ease-out;
    }

    .stat-progress.animate {
        width: 100%;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative min-h-screen flex items-center justify-center gradient-bg overflow-hidden">
    <!-- Particules flottantes -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-4 h-4 bg-white/20 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
        <div class="absolute top-40 right-20 w-6 h-6 bg-white/10 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-40 left-20 w-3 h-3 bg-white/30 rounded-full animate-bounce" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-5 h-5 bg-white/15 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
    </div>

    <div class="container mx-auto px-4 text-center text-white relative z-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fadeInUp">
                À propos de <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">Nova Tech</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90 animate-fadeInUp" style="animation-delay: 0.2s;">
                Votre partenaire technologique de confiance pour tous vos projets digitaux
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
                <a href="#story" class="nova-btn nova-btn-primary bg-white text-blue-600 hover:bg-gray-100">
                    <i class="fas fa-arrow-down mr-2"></i> Découvrir notre histoire
                </a>
                <a href="#team" class="nova-btn nova-btn-secondary border border-white text-white hover:bg-white hover:text-blue-600">
                    <i class="fas fa-users mr-2"></i> Rencontrer l'équipe
                </a>
            </div>
        </div>
    </div>

    <!-- Indicateur de scroll -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-white text-2xl"></i>
    </div>
</div>

<!-- Section Histoire et Mission -->
<section id="story" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Notre Histoire -->
            <div class="space-y-6 animated-item">
                <div class="inline-block">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">
                        Notre <span class="text-blue-600">Histoire</span>
                    </h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-green-500 rounded"></div>
                </div>
                <div class="space-y-4 text-gray-600 leading-relaxed">
                    <p class="text-lg">
                        Fondée avec la vision de démocratiser l'accès aux technologies digitales au Bénin,
                        Nova Tech s'est imposée comme un acteur incontournable du secteur technologique local.
                    </p>
                    <p>
                        Depuis nos débuts, nous accompagnons les entreprises béninoises et ouest-africaines dans
                        leur transformation digitale, en leur fournissant des solutions sur mesure et un
                        soutien technique de premier plan.
                    </p>
                </div>
                <div class="flex items-center space-x-4 pt-4">
                    <div class="flex -space-x-2">
                        <div class="w-10 h-10 bg-blue-500 rounded-full border-2 border-white"></div>
                        <div class="w-10 h-10 bg-green-500 rounded-full border-2 border-white"></div>
                        <div class="w-10 h-10 bg-purple-500 rounded-full border-2 border-white"></div>
                    </div>
                    <span class="text-sm text-gray-500">Une équipe passionnée à votre service</span>
                </div>
            </div>

            <!-- Mission et Vision -->
            <div class="bg-white rounded-3xl p-8 shadow-xl glow-card animated-item">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-bullseye text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Notre Mission</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Rendre la technologie accessible et impactante pour les entreprises, en créant des solutions qui stimulent leur croissance et leur efficacité.
                        </p>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-eye text-green-600 text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Notre Vision</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Devenir le leader technologique au Bénin et dans la sous-région, reconnu pour notre innovation, notre expertise et notre engagement envers la réussite de nos clients.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Statistiques -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-purple-800 text-white relative overflow-hidden">
    <!-- Motif de fond -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="white" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>');"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Nos Réalisations</h2>
            <p class="text-xl opacity-90">Des chiffres qui parlent de notre expertise</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Stat 1 -->
            <div class="text-center animated-item">
                <div class="relative mb-6">
                    <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <i class="fas fa-project-diagram text-3xl"></i>
                    </div>
                    <div class="stat-number text-5xl md:text-6xl font-bold mb-2" data-target="200">0</div>
                    <div class="w-full bg-white/20 rounded-full h-2 mx-auto max-w-xs">
                        <div class="stat-progress bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full"></div>
                    </div>
                </div>
                <div class="text-xl font-semibold">Projets Réalisés</div>
                <div class="text-sm opacity-75 mt-1">Solutions développées sur mesure</div>
            </div>

            <!-- Stat 2 -->
            <div class="text-center animated-item" style="animation-delay: 0.2s;">
                <div class="relative mb-6">
                    <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <i class="fas fa-heart text-3xl"></i>
                    </div>
                    <div class="stat-number text-5xl md:text-6xl font-bold mb-2" data-target="95">0</div>
                    <div class="w-full bg-white/20 rounded-full h-2 mx-auto max-w-xs">
                        <div class="stat-progress bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full"></div>
                    </div>
                </div>
                <div class="text-xl font-semibold">Satisfaction Client</div>
                <div class="text-sm opacity-75 mt-1">Taux de satisfaction exceptionnelle</div>
            </div>

            <!-- Stat 3 -->
            <div class="text-center animated-item" style="animation-delay: 0.4s;">
                <div class="relative mb-6">
                    <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <i class="fas fa-calendar-alt text-3xl"></i>
                    </div>
                    <div class="stat-number text-5xl md:text-6xl font-bold mb-2" data-target="5">0</div>
                    <div class="w-full bg-white/20 rounded-full h-2 mx-auto max-w-xs">
                        <div class="stat-progress bg-gradient-to-r from-purple-400 to-pink-500 h-2 rounded-full"></div>
                    </div>
                </div>
                <div class="text-xl font-semibold">Années d'Expérience</div>
                <div class="text-sm opacity-75 mt-1">Expertise éprouvée dans le domaine</div>
            </div>
        </div>
    </div>
</section>

<!-- Section Équipe -->
<section id="team" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Notre <span class="text-blue-600">Équipe</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Découvrez les visages derrière notre succès, une équipe de passionnés et d'experts dévoués à votre réussite.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($teamMembers as $index => $member)
                <div class="group animated-item" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 glow-card">
                        <!-- Photo du membre -->
                        <div class="relative mb-6">
                            <div class="w-32 h-32 mx-auto relative">
                                <img src="{{ asset($member->photo) }}"
                                     alt="{{ $member->name }}"
                                     class="w-full h-full object-cover rounded-2xl border-4 border-gradient-to-r from-blue-500 to-green-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-600/20 to-transparent rounded-2xl group-hover:from-blue-600/40 transition-all duration-300"></div>
                            </div>
                            <!-- Badge de statut -->
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                <div class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                </div>
                            </div>
                        </div>

                        <!-- Informations du membre -->
                        <div class="text-center mb-6">
                            <h4 class="text-xl font-bold text-gray-800 mb-1">{{ $member->name }}</h4>
                            <p class="text-blue-600 font-semibold mb-3">{{ $member->position }}</p>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $member->bio }}</p>
                        </div>

                        <!-- Compétences -->
                        <div class="space-y-3">
                            <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-code mr-2 text-blue-500"></i> Compétences
                            </h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($member->skillsArray as $skill)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full hover:bg-blue-200 transition-colors duration-200">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Liens sociaux (optionnel) -->
                        <div class="flex justify-center space-x-4 mt-6 pt-6 border-t border-gray-100">
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-all duration-200">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-all duration-200">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-500 hover:text-white transition-all duration-200">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Équipe en construction</h3>
                    <p class="text-gray-500">Les profils de notre équipe seront bientôt disponibles.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Section CTA -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Prêt à démarrer votre projet ?
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Collaborons ensemble pour transformer vos idées en solutions technologiques innovantes.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/contact') }}" class="nova-btn nova-btn-primary bg-white text-blue-600 hover:bg-gray-100 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-rocket mr-2"></i> Commencer maintenant
                </a>
                <a href="{{ url('/services') }}" class="nova-btn nova-btn-secondary border border-white text-white hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-eye mr-2"></i> Voir nos services
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des statistiques au scroll
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -50px 0px'
    };

    // Observer pour les animations d'entrée
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                animationObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer pour les statistiques
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                const statProgresses = entry.target.querySelectorAll('.stat-progress');

                // Animer les barres de progression
                statProgresses.forEach(progress => {
                    progress.classList.add('animate');
                });

                // Animer les chiffres
                statNumbers.forEach(stat => {
                    const target = parseInt(stat.getAttribute('data-target'));
                    const suffix = stat.textContent.includes('+') ? '+' :
                                  stat.textContent.includes('%') ? '%' : '';
                    animateCounter(stat, target, suffix);
                });

                statsObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Initialiser les éléments animés
    const animatedItems = document.querySelectorAll('.animated-item');
    animatedItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
        animationObserver.observe(item);
    });

    // Observer la section des statistiques
    const statsSection = document.querySelector('.py-20.bg-gradient-to-br');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Fonction d'animation des compteurs
    function animateCounter(element, target, suffix = '') {
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + suffix;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + suffix;
            }
        }, 16);
    }

    // Smooth scroll pour les liens internes
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Parallax léger pour le hero
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.gradient-bg');

        parallaxElements.forEach(element => {
            const speed = scrolled * -0.5;
            element.style.transform = `translateY(${speed}px)`;
        });
    });
});
</script>
@endpush
