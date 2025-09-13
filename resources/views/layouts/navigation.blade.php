<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <a class="navbar-brand app-brand demo" href="{{ route('home') }}">
        <span class="app-brand-logo demo">
            <!-- Your logo here -->
        </span>
        <span class="app-brand-text demo menu-text fw-bolder ms-2">Nova Tech</span>
    </a>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">Accueil</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Services
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('services.index') }}">Tous nos services</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('services.index') }}#development">Développement</a></li>
                    <li><a class="dropdown-item" href="{{ route('services.index') }}#hardware">Matériel informatique</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('projects.index') }}">Réalisations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}">Produits</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('about') }}">À propos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('contact') }}">Contact</a>
            </li>
        </ul>
    </div>
</nav>
