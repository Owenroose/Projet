<!DOCTYPE html>
<html lang="fr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard') - Nova Tech Bénin</title>

    <meta name="description" content="Tableau de bord administrateur de Nova Tech Bénin" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link href="{{ asset('assets/css/user-management.css') }}" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @yield('page-css')
    @stack('styles')

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                    <path
                                        d="M19.7891283,5.20464811 C19.6483563,5.08637777 19.4646864,5.03576759 19.2882296,5.07455823 L18.0163351,5.34114488 L18.0163351,0.92341999 L21.0116812,0.92341999 C21.1396115,0.92341999 21.2644788,0.974103323 21.3621437,1.0664877 L24.3809635,3.90382374 L24.512613,4.03264027 C24.6405364,4.16145681 24.7171439,4.33129188 24.7171439,4.50854406 C24.7171439,4.68579624 24.6405364,4.85563131 24.512613,4.98444785 L21.5791289,8.02641979 C21.439077,8.17068285 21.2404653,8.23238656 21.0416955,8.19632832 L19.7891283,5.20464811 Z"
                                        id="path-1"></path>
                                    <path
                                        d="M10.8718428,2.78404886 L8.52044322,0.432649281 C8.32420427,0.236410336 8.01254395,0.236410336 7.816305,0.432649281 C7.62006606,0.628888226 7.62006606,0.940548558 7.816305,1.1367875 L10.1677046,3.48818708 C10.3639435,3.68442603 10.6756039,3.68442603 10.8718428,3.48818708 C11.0680818,3.29194813 11.0680818,2.9802878 10.8718428,2.78404886 Z"
                                        id="path-2"></path>
                                </defs>
                                <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Path-4" fill="#696cff" transform="translate(19.200000, 4.000000) rotate(-180.000000) translate(-19.200000, -4.000000) ">
                                        <use xlink:href="#path-1"></use>
                                    </g>
                                    <g id="Path-3" fill="#696cff"
                                        transform="translate(5.800000, 2.000000) rotate(-180.000000) translate(-5.800000, -2.000000) ">
                                        <use xlink:href="#path-2"></use>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">NovaTech</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Tableau de bord</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestion commerciale</span>
                    </li>

                    <!-- Menu Commandes avec sous-menu -->
                    <li class="menu-item {{ request()->routeIs('admin.orders*') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
                            <div data-i18n="Orders">Commandes</div>
                            @php
                                $pendingOrdersCount = \App\Models\Order::select('order_group')->distinct()->where('status', 'pending')->count();
                            @endphp
                            @if($pendingOrdersCount > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">{{ $pendingOrdersCount }}</span>
                            @endif
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ request()->routeIs('admin.orders.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.dashboard') }}" class="menu-link">
                                    <div data-i18n="Orders Dashboard">Dashboard</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.orders.index') && !request()->get('status') ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index') }}" class="menu-link">
                                    <div data-i18n="All Orders">Toutes les commandes</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.orders.index') && request()->get('status') == 'pending' ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="menu-link">
                                    <div data-i18n="Pending Orders">En attente</div>
                                    @if($pendingOrdersCount > 0)
                                        <span class="badge bg-warning rounded-pill ms-auto">{{ $pendingOrdersCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.orders.index') && request()->get('status') == 'processing' ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="menu-link">
                                    <div data-i18n="Processing Orders">En cours</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.orders.index') && request()->get('status') == 'shipped' ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="menu-link">
                                    <div data-i18n="Shipped Orders">Expédiées</div>
                                </a>
                            </li>
                            <li class="menu-item {{ request()->routeIs('admin.orders.index') && request()->get('status') == 'delivered' ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="menu-link">
                                    <div data-i18n="Delivered Orders">Livrées</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestion de contenu</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        <a href="{{ route('admin.products.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-box"></i>
                            <div data-i18n="Products">Produits</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.projects*') ? 'active' : '' }}">
                        <a href="{{ route('admin.projects.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-folder"></i>
                            <div data-i18n="Projects">Projets</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                        <a href="{{ route('admin.services.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cog"></i>
                            <div data-i18n="Services">Services</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}">
                        <a href="{{ route('admin.testimonials.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-star"></i>
                            <div data-i18n="Testimonials">Témoignages</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.team*') ? 'active' : '' }}">
                        <a href="{{ route('admin.team-members.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Team">Équipe</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                        <a href="{{ route('admin.contacts.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-envelope"></i>
                            <div data-i18n="Contacts">Messages</div>
                            @php($unreadCount = \App\Models\Contact::where('read', false)->count())
                            @if($unreadCount > 0)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.ai-assistant*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-link" onclick="toggleAIAssistant()">
                          <i class="menu-icon tf-icons bx bx-brain"></i>
                          <div data-i18n="AI Assistant">Assistant IA</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Gestion de l'application</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div data-i18n="Users">Utilisateurs</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-shield-alt-2"></i>
                            <div data-i18n="Roles">Rôles</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Paramètres</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                        <a href="{{ route('admin.profile.edit') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-circle"></i>
                            <div data-i18n="Profile">Profil</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="menu-link"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="menu-icon tf-icons bx bx-log-out"></i>
                                <div data-i18n="Logout">Déconnexion</div>
                            </a>
                        </form>
                    </li>
                </ul>
            </aside>
            <div class="layout-page">
                @include('admin.layouts.header')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Section Assistant IA -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4 ai-assistant-section d-none">
                                  <h5 class="card-header">Assistant IA</h5>
                                  <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                      <div class="flex-grow-1 me-3">
                                        <div class="input-group">
                                          <input id="ai-prompt-input" type="text" class="form-control" placeholder="Posez une question à l'assistant IA..." autofocus>
                                          <button id="ai-generate-button" class="btn btn-primary" type="button">Générer</button>
                                        </div>
                                      </div>
                                      <button id="ai-close-button" class="btn btn-icon btn-outline-secondary" type="button">
                                        <i class="bx bx-x"></i>
                                      </button>
                                    </div>
                                    <div id="ai-response-area" class="alert alert-info mt-3" style="display: none;">
                                      <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                      </div>
                                      <span id="ai-response-text"></span>
                                    </div>
                                    <div id="ai-error-area" class="alert alert-danger mt-3" style="display: none;">
                                      <span id="ai-error-text"></span>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Flash -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bx bx-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Contenu principal -->
                        @yield('content')
                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                document.write(new Date().getFullYear());
                                </script>
                                , fait avec ❤️ par
                                <a href="https://novatech-benin.com" target="_blank"
                                    class="footer-link fw-bolder">Nova Tech Bénin</a>
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <script>
      const aiAssistantSection = document.querySelector('.ai-assistant-section');
      const aiPromptInput = document.getElementById('ai-prompt-input');
      const aiGenerateButton = document.getElementById('ai-generate-button');
      const aiResponseArea = document.getElementById('ai-response-area');
      const aiResponseText = document.getElementById('ai-response-text');
      const aiErrorArea = document.getElementById('ai-error-area');
      const aiErrorText = document.getElementById('ai-error-text');
      const aiCloseButton = document.getElementById('ai-close-button');

      function toggleAIAssistant() {
        if (aiAssistantSection.classList.contains('d-none')) {
          aiAssistantSection.classList.remove('d-none');
          aiAssistantSection.scrollIntoView({ behavior: 'smooth' });
          aiPromptInput.focus();
        } else {
          aiAssistantSection.classList.add('d-none');
          aiResponseArea.style.display = 'none';
          aiErrorArea.style.display = 'none';
          aiPromptInput.value = '';
        }
      }

      function showAlert(message, type = 'info') {
        const alertContainer = document.querySelector('.container-xxl.flex-grow-1.container-p-y');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
          <i class="bx bx-${type === 'success' ? 'check' : type === 'error' ? 'error' : 'info'}-circle me-2"></i>
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.insertBefore(alertDiv, alertContainer.firstChild);

        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.remove();
          }
        }, 5000);
      }

      // Gestionnaire d'événements pour l'assistant IA
      aiPromptInput.addEventListener('keydown', (event) => {
        if (event.ctrlKey && event.key === 'Enter') {
          event.preventDefault();
          aiGenerateButton.click();
        }
      });

      // Fermer l'assistant IA
      aiCloseButton.addEventListener('click', () => {
        toggleAIAssistant();
      });

      // Auto-dismiss des alertes après 5 secondes
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          const alerts = document.querySelectorAll('.alert:not(.ai-assistant-section .alert)');
          alerts.forEach(function(alert) {
            if (alert.querySelector('.btn-close')) {
              alert.querySelector('.btn-close').click();
            }
          });
        }, 5000);
      });
    </script>

    @yield('page-js')
    @stack('scripts')

    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
