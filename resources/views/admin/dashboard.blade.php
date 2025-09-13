@extends('admin.layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accueil /</span> Tableau de bord
    </h4>

    <div class="row g-4 mb-4">
        <div class="col-lg-8 order-2 order-md-1">
            <div class="card h-100 p-4">
                <div class="card-body">
                    <h2 class="card-title text-nova-primary fw-bold mb-2">Bienvenue, {{ Auth::user()->name }} ! 👋</h2>
                    <p class="mb-4 text-muted">Voici un aperçu rapide des activités récentes de votre site.</p>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-success p-2 rounded me-3"><i class="bx bx-cog fs-4"></i></span>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $stats['services'] }} Services</p>
                                    <small class="text-muted">Total de services actifs</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-warning p-2 rounded me-3"><i class="bx bx-box fs-4"></i></span>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $stats['products'] }} Produits</p>
                                    <small class="text-muted">Produits listés</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-info p-2 rounded me-3"><i class="bx bx-folder-open fs-4"></i></span>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $stats['projects'] }} Projets</p>
                                    <small class="text-muted">Projets achevés</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 order-1 order-md-2">
            <div class="card h-100 p-4 bg-primary text-white">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <div class="avatar-icon mb-3 mx-auto">
                        <i class="bx bx-brain fs-1 text-white"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">Besoin d'aide ?</h5>
                    <p class="text-white-50 mb-4">Utilisez votre assistant IA pour toute question technique ou de développement.</p>
                    <button class="btn btn-light" onclick="toggleAIAssistant()">Ouvrir l'Assistant IA</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Statistiques de Contenu</h5>
                    <small class="text-muted">Répartition des Services, Produits et Projets</small>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:30vh; width:100%">
                        <canvas id="contentDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Messages Récents</h5>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($recentContacts as $contact)
                                <tr>
                                    <td><a href="{{ route('admin.contacts.show', $contact->id) }}">{{ $contact->name }}</a></td>
                                    <td>{{ $contact->email }}</td>
                                    <td>
                                        @if(!$contact->read)
                                            <span class="badge bg-danger">Non lu</span>
                                        @else
                                            <span class="badge bg-success">Lu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun message récent.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>

@endsection

@section('page-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Données passées par le contrôleur
            const chartData = @json($chartData);

            // Initialisation du graphique
            const ctx = document.getElementById('contentDistributionChart');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Services', 'Produits', 'Projets'],
                    datasets: [{
                        data: [chartData.services, chartData.products, chartData.projects],
                        backgroundColor: [
                            'rgba(0, 119, 182, 0.8)', // Bleu pour les services
                            'rgba(251, 176, 64, 0.8)', // Jaune pour les produits
                            'rgba(217, 83, 79, 0.8)'  // Rouge pour les projets
                        ],
                        hoverBackgroundColor: [
                            'rgba(0, 119, 182, 1)',
                            'rgba(251, 176, 64, 1)',
                            'rgba(217, 83, 79, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' entités';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
