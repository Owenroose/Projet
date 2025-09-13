<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Nova Tech Bénin') - Votre partenaire technologique</title>
    <meta name="keywords" content="Nova Tech, développement web, mobile, matériel informatique, Bénin">
    <meta name="description" content="@yield('description', 'Nova Tech Bénin offre des solutions de développement web et mobile sur mesure ainsi que la vente de matériel informatique de qualité.')">
    <meta name="author" content="Nova Tech Bénin">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://cdn.fedapay.com/checkout.js"></script>

    <style type="text/tailwindcss">
        @layer components {
            .nova-btn {
                @apply inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-semibold transition-all duration-300;
            }
            .nova-btn-primary {
                @apply bg-blue-600 text-white shadow-md hover:bg-blue-700 hover:shadow-lg;
            }
            .nova-btn-secondary {
                @apply bg-green-500 text-white shadow-md hover:bg-green-600 hover:shadow-lg;
            }

            /* Améliorations pour le logo */
            .logo-container {
                @apply flex-shrink-0 transition-all duration-300;
            }

            .logo-img {
                @apply h-12 md:h-14 w-auto object-contain transition-all duration-300;
            }

            /* Variantes du logo selon l'état de l'header */
            /* Initialement, le logo est dans un wrapper semi-transparent */
            .header:not(.scrolled) .logo-img.dark {
                @apply block; /* Affiche le logo sombre */
            }
            .header:not(.scrolled) .logo-img.light {
                @apply hidden; /* Cache le logo clair */
            }

            /* Quand l'header est scrolled, on cache le logo sombre et affiche le clair */
            .header.scrolled .logo-img.dark {
                @apply hidden; /* Cache le logo sombre */
            }
            .header.scrolled .logo-img.light {
                @apply block; /* Affiche le logo clair */
            }

            .header.scrolled .logo-img {
                @apply h-10 md:h-12;
            }
        }

        /* Styles personnalisés globaux */
        body {
            font-family: 'Inter', sans-serif;
            @apply antialiased text-gray-800;
        }

        /* En-tête avec fond adaptatif */
        .header {
            @apply fixed top-0 left-0 w-full z-50 transition-all duration-500;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.95) 0%, rgba(16, 185, 129, 0.95) 100%);
            backdrop-filter: blur(10px);
        }

        /* CHANGEMENT ICI : Modification de la couleur du header lors du défilement */
        .header.scrolled {
            @apply bg-gray-900 shadow-xl; /* Utilisez une couleur foncée comme bg-gray-900 */
            background: rgba(17, 24, 39, 0.95); /* Une version semi-transparente */
            backdrop-filter: blur(20px);
        }

        /* CHANGEMENT ICI : Mise à jour des couleurs des liens de navigation pour le header défilé */
        .header.scrolled .md\\:flex a:not(.nova-btn) {
            @apply text-gray-200 hover:text-blue-400;
        }
        .header.scrolled .md\\:flex a.font-bold {
            @apply text-yellow-300;
        }

        /* Logo responsive et adaptatif */
        .logo-wrapper {
            @apply relative overflow-hidden rounded-lg;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            padding: 8px 12px;
        }

        .header.scrolled .logo-wrapper {
            background: transparent;
            padding: 4px 8px;
        }

        /* Loader */
        .loader_bg {
            @apply fixed top-0 left-0 w-full h-full bg-white z-[9999] flex items-center justify-center;
        }
        .loader {
            @apply w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin;
        }

        /* Menu mobile amélioré */
        .mobile-menu {
            @apply fixed inset-x-0 top-0 z-40 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out;
            padding-top: 80px;
        }

        .mobile-menu.hidden {
            @apply -translate-y-full;
        }

        .mobile-menu:not(.hidden) {
            @apply translate-y-0;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="loader_bg">
        <div class="loader"></div>
    </div>

    <header class="header">
        <nav class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="logo-container">
                <div class="logo-wrapper">
                    {{-- Logo sombre, visible par défaut --}}
                    <img src="{{ asset('images/nova-tech-logo.png') }}"
                         alt="Nova Tech Bénin"
                         class="logo-img dark"
                         onerror="this.src='{{ asset('images/nova-tech-logo-white.png') }}';">

                    {{-- Logo clair, visible quand le header est scrollé --}}
                    <img src="{{ asset('images/nova-tech-logo-white.png') }}"
                         alt="Nova Tech Bénin"
                         class="logo-img light hidden"
                         onerror="this.src='{{ asset('images/nova-tech-logo.png') }}';">
                </div>
            </a>

            <button class="md:hidden text-white focus:outline-none z-50 relative"
                    onclick="toggleMobileMenu()">
                <i id="menu-icon" class="fas fa-bars fa-2x transition-transform duration-300"></i>
            </button>

            <div class="hidden md:flex md:items-center space-x-8">
                <a href="{{ url('/') }}"
                   class="text-white hover:text-blue-200 transition-colors duration-200 font-medium
                   @if(Request::is('/')) font-bold text-yellow-300 @endif">
                   Accueil
                </a>
                <a href="{{ url('/about') }}"
                   class="text-white hover:text-blue-200 transition-colors duration-200 font-medium
                   @if(Request::is('about')) font-bold text-yellow-300 @endif">
                   À propos
                </a>
                <a href="{{ url('/services') }}"
                   class="text-white hover:text-blue-200 transition-colors duration-200 font-medium
                   @if(Request::is('services')) font-bold text-yellow-300 @endif">
                   Services
                </a>
                <a href="{{ url('/products') }}"
                   class="text-white hover:text-blue-200 transition-colors duration-200 font-medium
                   @if(Request::is('products')) font-bold text-yellow-300 @endif">
                   Produits
                </a>
                <a href="{{ url('/projects') }}"
                   class="text-white hover:text-blue-200 transition-colors duration-200 font-medium
                   @if(Request::is('projects')) font-bold text-yellow-300 @endif">
                   Projets
                </a>
                <a href="{{ url('/contact') }}"
                   class="nova-btn nova-btn-primary bg-white text-blue-600 hover:bg-blue-50 hover:text-blue-700">
                   Contactez-nous
                </a>
            </div>

            <div id="mobile-menu" class="mobile-menu hidden">
                <div class="px-6 py-8 space-y-6 bg-gradient-to-br from-slate-800 via-slate-900 to-blue-900">
                    <a href="{{ url('/') }}"
                       class="block text-xl text-slate-200 hover:text-cyan-300 font-medium transition-all duration-300 py-3 px-4 rounded-lg hover:bg-slate-700/50
                       @if(Request::is('/')) text-cyan-300 font-semibold bg-slate-700/30 @endif"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-home mr-3"></i>Accueil
                    </a>
                    <a href="{{ url('/about') }}"
                       class="block text-xl text-slate-200 hover:text-cyan-300 font-medium transition-all duration-300 py-3 px-4 rounded-lg hover:bg-slate-700/50
                       @if(Request::is('about')) text-cyan-300 font-semibold bg-slate-700/30 @endif"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-info-circle mr-3"></i>À propos
                    </a>
                    <a href="{{ url('/services') }}"
                       class="block text-xl text-slate-200 hover:text-cyan-300 font-medium transition-all duration-300 py-3 px-4 rounded-lg hover:bg-slate-700/50
                       @if(Request::is('services')) text-cyan-300 font-semibold bg-slate-700/30 @endif"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-cogs mr-3"></i>Services
                    </a>
                    <a href="{{ url('/products') }}"
                       class="block text-xl text-slate-200 hover:text-cyan-300 font-medium transition-all duration-300 py-3 px-4 rounded-lg hover:bg-slate-700/50
                       @if(Request::is('products')) text-cyan-300 font-semibold bg-slate-700/30 @endif"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-box mr-3"></i>Produits
                    </a>
                    <a href="{{ url('/projects') }}"
                       class="block text-xl text-slate-200 hover:text-cyan-300 font-medium transition-all duration-300 py-3 px-4 rounded-lg hover:bg-slate-700/50
                       @if(Request::is('projects')) text-cyan-300 font-semibold bg-slate-700/30 @endif"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-project-diagram mr-3"></i>Projets
                    </a>
                    <a href="{{ url('/contact') }}"
                       class="block nova-btn bg-gradient-to-r from-cyan-500 to-blue-600 text-white hover:from-cyan-600 hover:to-blue-700 mt-6 w-full text-center shadow-lg transform hover:scale-105"
                       onclick="toggleMobileMenu()">
                       <i class="fas fa-envelope mr-2"></i>Contactez-nous
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="pt-24">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-300 py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg inline-block">
                        <img src="{{ asset('images/nova-tech-logo.png') }}"
                             alt="Nova Tech Bénin"
                             class="h-16 w-auto"
                             onerror="this.src='{{ asset('images/nova-tech-logo-white.png') }}';">
                    </div>
                    <p class="text-base leading-relaxed">Votre partenaire technologique pour l'avenir. Innovation, qualité et service client au cœur de nos priorités.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-2xl hover:text-blue-400 transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-2xl hover:text-blue-400 transition-colors duration-200"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-2xl hover:text-blue-400 transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-2xl hover:text-blue-400 transition-colors duration-200"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-white mb-6">Navigation</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-home mr-2"></i>Accueil</a></li>
                        <li><a href="{{ url('/about') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-info-circle mr-2"></i>À propos</a></li>
                        <li><a href="{{ url('/services') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-cogs mr-2"></i>Services</a></li>
                        <li><a href="{{ url('/products') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-box mr-2"></i>Produits</a></li>
                        <li><a href="{{ url('/projects') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-project-diagram mr-2"></i>Projets</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-white transition-colors duration-200 flex items-center"><i class="fas fa-envelope mr-2"></i>Contact</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-white mb-6">Newsletter</h3>
                    <p class="text-sm mb-4">Restez informé de nos dernières actualités et offres.</p>
                    <form class="space-y-3">
                        <input class="w-full p-3 rounded-lg bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               placeholder="Votre adresse email"
                               type="email"
                               name="email">
                        <button type="submit" class="nova-btn nova-btn-secondary w-full">
                            <i class="fas fa-paper-plane mr-2"></i>S'abonner
                        </button>
                    </form>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-white mb-6">Contact</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-blue-400 mt-1"></i>
                            <span>Cotonou, Bénin<br><small class="text-gray-400">Quartier Fidjrossè</small></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-green-400"></i>
                            <span>+229 97 00 00 00</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-red-400"></i>
                            <span>contact@novatechbenin.com</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-yellow-400"></i>
                            <span class="text-sm">Lun-Ven: 8h-18h<br>Sam: 8h-12h</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-center md:text-left">
                        Copyright © {{ date('Y') }}
                        <a href="{{ url('/') }}" class="text-blue-400 hover:underline font-semibold">Nova Tech Bénin</a>.
                        Tous droits réservés.
                    </p>
                    <div class="flex space-x-6 text-sm">
                        <a href="#" class="hover:text-white transition-colors">Politique de confidentialité</a>
                        <a href="#" class="hover:text-white transition-colors">Mentions légales</a>
                        <a href="#" class="hover:text-white transition-colors">CGV</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    @stack('scripts')

    <script>
        // Variables globales
        let mobileMenuOpen = false;

        document.addEventListener('DOMContentLoaded', () => {
            // Cacher le loader après chargement
            setTimeout(() => {
                const loader = document.querySelector('.loader_bg');
                if (loader) {
                    loader.style.display = 'none';
                }
            }, 1000);

            // Gérer le défilement de l'en-tête
            const header = document.querySelector('.header');
            if (header) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                });
            }

            // Gérer les liens de défilement doux
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

            // Fermer le menu mobile en cliquant en dehors
            document.addEventListener('click', (e) => {
                const mobileMenu = document.getElementById('mobile-menu');
                const menuButton = e.target.closest('button');

                if (mobileMenuOpen && !mobileMenu.contains(e.target) && !menuButton) {
                    toggleMobileMenu();
                }
            });
        });

        // Fonction pour toggle le menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');

            if (mobileMenu && menuIcon) {
                mobileMenuOpen = !mobileMenuOpen;

                if (mobileMenuOpen) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-times');
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                    document.body.style.overflow = '';
                }
            }
        }

        // Gestion des erreurs d'images
        document.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG') {
                console.warn('Erreur de chargement d\'image:', e.target.src);
                // Fallback vers une image par défaut si nécessaire
                if (!e.target.src.includes('placeholder')) {
                    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vdmEgVGVjaCBCw6luaW48L3RleHQ+PC9zdmc+';
                }
            }
        }, true);
    </script>
</body>
</html>
