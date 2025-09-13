@extends('admin.layouts.app')

@section('title', 'Dashboard des Commandes')

@push('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --info-color: #0891b2;
        --dark-color: #1e293b;
        --light-color: #f8fafc;
        --border-color: #e2e8f0;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-4: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .dashboard-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-1);
    }

    .header-title h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--gradient-1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .header-subtitle {
        color: var(--secondary-color);
        font-size: 1.1rem;
        font-weight: 500;
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-modern:hover::before {
        left: 100%;
    }

    .btn-primary-modern {
        background: var(--gradient-1);
        color: white;
    }

    .btn-secondary-modern {
        background: rgba(100, 116, 139, 0.1);
        color: var(--secondary-color);
        border: 2px solid var(--border-color);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .metric-card.primary::before { background: var(--gradient-1); }
    .metric-card.success::before { background: var(--gradient-4); }
    .metric-card.warning::before { background: var(--gradient-2); }
    .metric-card.info::before { background: var(--gradient-3); }

    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .metric-icon.primary { background: var(--gradient-1); }
    .metric-icon.success { background: var(--gradient-4); }
    .metric-icon.warning { background: var(--gradient-2); }
    .metric-icon.info { background: var(--gradient-3); }

    .metric-value {
        font-size: 3rem;
        font-weight: 800;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .metric-label {
        font-size: 0.9rem;
        color: var(--secondary-color);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-subtitle {
        font-size: 1rem;
        color: var(--secondary-color);
        margin-top: 0.5rem;
    }

    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        height: 100%;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .chart-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: var(--light-color);
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .status-item:hover {
        background: white;
        box-shadow: var(--shadow);
    }

    .status-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 1rem;
    }

    .priority-alert {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.9; }
    }

    .priority-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .priority-value {
        font-size: 3rem;
        font-weight: 900;
        color: #d97706;
        margin-bottom: 0.5rem;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 2rem;
        border-bottom: 2px solid var(--border-color);
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table th {
        background: var(--light-color);
        padding: 1rem 1.5rem;
        font-weight: 700;
        color: var(--secondary-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.8rem;
        border-bottom: 2px solid var(--border-color);
    }

    .modern-table td {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .modern-table tbody tr:hover {
        background: var(--light-color);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-processing { background: #dbeafe; color: #2563eb; }
    .badge-shipped { background: #f3e8ff; color: #7c3aed; }
    .badge-delivered { background: #d1fae5; color: #059669; }
    .badge-cancelled { background: #fee2e2; color: #dc2626; }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        text-decoration: none;
        color: var(--dark-color);
        transition: all 0.3s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
        display: block;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient-1);
        transition: left 0.3s;
        z-index: 1;
    }

    .action-btn:hover::before {
        left: 0;
    }

    .action-btn:hover {
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        text-decoration: none;
    }

    .action-btn i,
    .action-btn span {
        position: relative;
        z-index: 2;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1rem;
        }

        .header-title h1 {
            font-size: 2rem;
        }

        .metric-card {
            padding: 1.5rem;
        }

        .chart-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="header-title">
                <h1>Dashboard des Commandes</h1>
                <p class="header-subtitle">Vue d'ensemble complète de l'activité commerciale</p>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('admin.orders.index') }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-list"></i>
                    Toutes les commandes
                </a>
                <button class="btn-modern btn-secondary-modern" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="metric-card primary">
                <div class="metric-header">
                    <div>
                        <div class="metric-label">Aujourd'hui</div>
                        <div class="metric-value">{{ number_format($stats['today']['count']) }}</div>
                        <div class="metric-subtitle">{{ number_format($stats['today']['revenue']) }} FCFA</div>
                    </div>
                    <div class="metric-icon primary">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card success">
                <div class="metric-header">
                    <div>
                        <div class="metric-label">Cette semaine</div>
                        <div class="metric-value">{{ number_format($stats['week']['count']) }}</div>
                        <div class="metric-subtitle">{{ number_format($stats['week']['revenue']) }} FCFA</div>
                    </div>
                    <div class="metric-icon success">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card warning">
                <div class="metric-header">
                    <div>
                        <div class="metric-label">Ce mois</div>
                        <div class="metric-value">{{ number_format($stats['month']['count']) }}</div>
                        <div class="metric-subtitle">{{ number_format($stats['month']['revenue']) }} FCFA</div>
                    </div>
                    <div class="metric-icon warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card info">
                <div class="metric-header">
                    <div>
                        <div class="metric-label">Revenus total</div>
                        <div class="metric-value">{{ number_format($stats['total_revenue'] / 1000000, 1) }}M</div>
                        <div class="metric-subtitle">FCFA</div>
                    </div>
                    <div class="metric-icon info">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Évolution des commandes (7 derniers jours)</h3>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #2563eb; margin-right: 0.5rem;"></div>
                            <span style="font-size: 0.9rem; font-weight: 500;">Commandes</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #059669; margin-right: 0.5rem;"></div>
                            <span style="font-size: 0.9rem; font-weight: 500;">Revenus (M FCFA)</span>
                        </div>
                    </div>
                </div>
                <div style="height: 400px; position: relative;">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-container mb-4">
                <div class="chart-header">
                    <h3 class="chart-title">Statut des commandes</h3>
                </div>

                <div>
                    <div class="status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #d97706;"></div>
                            <span style="font-weight: 600;">En attente</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ $stats['by_status']['pending'] }}</span>
                    </div>

                    <div class="status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #2563eb;"></div>
                            <span style="font-weight: 600;">En cours</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ $stats['by_status']['processing'] }}</span>
                    </div>

                    <div class="status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #7c3aed;"></div>
                            <span style="font-weight: 600;">Expédiée</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ $stats['by_status']['shipped'] }}</span>
                    </div>

                    <div class="status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #059669;"></div>
                            <span style="font-weight: 600;">Livrée</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ $stats['by_status']['delivered'] }}</span>
                    </div>

                    <div class="status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-dot" style="background: #dc2626;"></div>
                            <span style="font-weight: 600;">Annulée</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ $stats['by_status']['cancelled'] }}</span>
                    </div>
                </div>

                <div style="height: 200px; margin-top: 2rem;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            @if($stats['priority'] > 0)
            <div class="priority-alert">
                <div class="priority-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="priority-value">{{ $stats['priority'] }}</div>
                <p style="font-weight: 600; color: #d97706; margin-bottom: 1rem;">Commandes prioritaires</p>
                <p style="color: #92400e; font-size: 0.9rem; margin-bottom: 1.5rem;">Nécessitent une attention immédiate</p>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                   class="btn-modern btn-primary-modern">
                    <i class="fas fa-arrow-right"></i>
                    Voir les commandes
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="table-container mb-4">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="table-title">Commandes récentes</h3>
                    <p style="color: var(--secondary-color); margin: 0;">Dernières transactions enregistrées</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn-modern btn-primary-modern">
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->is_priority)
                                    <i class="fas fa-star text-warning me-2"></i>
                                @endif
                                <strong>#{{ $order->id }}</strong>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div style="font-weight: 600;">{{ $order->name }}</div>
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">{{ $order->email }}</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div>{{ Str::limit($order->product->name, 30) }}</div>
                                <div style="color: var(--secondary-color); font-size: 0.9rem;">Qté: {{ $order->quantity }}</div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ number_format($order->total_price) }} FCFA</strong>
                        </td>
                        <td>
                            <span class="status-badge
                                @if($order->status == 'pending') badge-pending
                                @elseif($order->status == 'processing') badge-processing
                                @elseif($order->status == 'shipped') badge-shipped
                                @elseif($order->status == 'delivered') badge-delivered
                                @elseif($order->status == 'cancelled') badge-cancelled
                                @endif">
                                @switch($order->status)
                                    @case('pending') En attente @break
                                    @case('processing') En cours @break
                                    @case('shipped') Expédiée @break
                                    @case('delivered') Livrée @break
                                    @case('cancelled') Annulée @break
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="btn-modern btn-primary-modern"
                               style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                Voir détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div style="color: var(--secondary-color);">
                                <i class="fas fa-shopping-cart" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>Aucune commande récente</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="chart-container">
        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--dark-color); margin-bottom: 2rem;">Actions rapides</h3>

        <div class="actions-grid">
            <button onclick="generateQuickReport()" class="action-btn">
                <i class="fas fa-file-excel" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <div>Rapport mensuel</div>
            </button>

            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="action-btn">
                <i class="fas fa-clock" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <div>Commandes en attente</div>
            </a>

            <button onclick="sendBulkEmail()" class="action-btn">
                <i class="fas fa-envelope" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <div>Email groupé</div>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour les graphiques
const chartData = @json($chartData);
const statusData = @json($stats['by_status']);

// Configuration du graphique en ligne
const ctx = document.getElementById('ordersChart').getContext('2d');
const ordersChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.map(item => item.date),
        datasets: [
            {
                label: 'Nombre de commandes',
                data: chartData.map(item => item.orders),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6
            },
            {
                label: 'Revenus (M FCFA)',
                data: chartData.map(item => Math.round(item.revenue / 1000000 * 10) / 10),
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1',
                pointBackgroundColor: '#059669',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        weight: 600
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Nombre de commandes',
                    font: {
                        weight: 600
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Revenus (M FCFA)',
                    font: {
                        weight: 600
                    }
                },
                grid: {
                    drawOnChartArea: false,
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        }
    }
});

// Configuration du graphique en donuts
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['En attente', 'En cours', 'Expédiée', 'Livrée', 'Annulée'],
        datasets: [{
            data: [
                statusData.pending,
                statusData.processing,
                statusData.shipped,
                statusData.delivered,
                statusData.cancelled
            ],
            backgroundColor: [
                '#d97706',
                '#2563eb',
                '#7c3aed',
                '#059669',
                '#dc2626'
            ],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverBorderWidth: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        weight: 600
                    }
                }
            }
        },
        cutout: '60%',
        elements: {
            arc: {
                hoverBorderWidth: 6
            }
        }
    }
});

// Fonctions utilitaires
function refreshDashboard() {
    // Afficher le loader
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('active');
    }

    setTimeout(() => {
        location.reload();
    }, 500);
}

function generateQuickReport() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.orders.generate-report") }}';

    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = '_token';
    csrfField.value = document.querySelector('meta[name="csrf-token"]').content;

    const dateFromField = document.createElement('input');
    dateFromField.type = 'hidden';
    dateFromField.name = 'date_from';
    dateFromField.value = firstDay.toISOString().split('T')[0];

    const dateToField = document.createElement('input');
    dateToField.type = 'hidden';
    dateToField.name = 'date_to';
    dateToField.value = lastDay.toISOString().split('T')[0];

    const formatField = document.createElement('input');
    formatField.type = 'hidden';
    formatField.name = 'format';
    formatField.value = 'excel';

    form.appendChild(csrfField);
    form.appendChild(dateFromField);
    form.appendChild(dateToField);
    form.appendChild(formatField);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function sendBulkEmail() {
    // Simulation d'une fonctionnalité d'email groupé
    if (confirm('Voulez-vous envoyer un email groupé aux clients ayant des commandes en attente ?')) {
        // Ici vous pouvez implémenter la logique d'envoi d'email groupé
        alert('Fonctionnalité d\'email groupé en développement. Cette fonctionnalité sera bientôt disponible.');
    }
}

// Animation d'entrée pour les cartes métriques
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.metric-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation pour les graphiques
    setTimeout(() => {
        ordersChart.update('active');
        statusChart.update('active');
    }, 800);
});

// Auto-refresh du dashboard toutes les 5 minutes
setInterval(function() {
    console.log('Dashboard auto-refresh...');
    // Vous pouvez implémenter une actualisation AJAX ici si nécessaire
}, 300000);

// Gestion des erreurs de graphiques
window.addEventListener('error', function(e) {
    if (e.message.includes('Chart')) {
        console.error('Erreur dans les graphiques:', e.message);
    }
});

// Loader overlay pour les transitions
document.addEventListener('DOMContentLoaded', function() {
    // Créer le loader si il n'existe pas
    if (!document.getElementById('loadingOverlay')) {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loadingOverlay);
    }
});

// Notifications toast personnalisées
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto-remove après 5 secondes
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Raccourcis clavier
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'r':
                e.preventDefault();
                refreshDashboard();
                break;
            case 'o':
                e.preventDefault();
                window.location.href = '{{ route("admin.orders.index") }}';
                break;
        }
    }
});

// Optimisation des performances pour mobile
if (window.innerWidth < 768) {
    // Réduire la fréquence d'animation sur mobile
    document.querySelectorAll('.metric-card').forEach(card => {
        card.style.transition = 'transform 0.2s ease';
    });
}

console.log('Dashboard Nova Tech - Chargé avec succès');
</script>

<div id="loadingOverlay" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center"
     style="top: 0; left: 0; background: rgba(255,255,255,0.9); z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s ease;">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Chargement...</span>
    </div>
</div>

<style>
    .loading-overlay.active {
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Animations supplémentaires */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .metric-card {
        animation: slideInUp 0.6s ease-out;
    }

    /* Responsive improvements */
    @media (max-width: 576px) {
        .dashboard-header {
            padding: 1rem;
        }

        .header-title h1 {
            font-size: 1.8rem;
        }

        .metric-value {
            font-size: 2rem;
        }

        .chart-container {
            padding: 1rem;
        }

        .modern-table th,
        .modern-table td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }
    }

    /* Dark mode support (optionnel) */
    @media (prefers-color-scheme: dark) {
        /* Vous pouvez ajouter les styles dark mode ici si nécessaire */
    }
</style>
@endpush
