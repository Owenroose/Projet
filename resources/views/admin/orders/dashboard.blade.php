@extends('admin.layouts.app')

@section('title', 'Dashboard des Commandes')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #3b82f6;
        --primary-light: #dbeafe;
        --secondary: #64748b;
        --success: #10b981;
        --success-light: #d1fae5;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --info: #06b6d4;
        --info-light: #cffafe;
        --dark: #0f172a;
        --light: #f8fafc;
        --white: #ffffff;
        --border: #e2e8f0;
        --gradient-blue: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-orange: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #f1f5f9;
        line-height: 1.6;
    }

    .dashboard-container {
        min-height: 100vh;
        padding: 2rem;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.1;
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 1rem 1rem 0 0;
    }

    .stat-card.total-orders::before { background: var(--gradient-blue); }
    .stat-card.total-sales::before { background: var(--gradient-green); }
    .stat-card.pending-orders::before { background: var(--gradient-orange); }
    .stat-card.priority-orders::before { background: var(--gradient-purple); }

    .stat-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.blue { background: var(--gradient-blue); }
    .stat-icon.green { background: var(--gradient-green); }
    .stat-icon.orange { background: var(--gradient-orange); }
    .stat-icon.purple { background: var(--gradient-purple); }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--secondary);
        font-weight: 500;
        font-size: 0.9rem;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .stat-change.positive {
        color: var(--success);
    }

    .stat-change.negative {
        color: var(--danger);
    }

    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-card {
        background: var(--white);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
    }

    .chart-subtitle {
        color: var(--secondary);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .recent-orders-card {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .card-header-custom {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-container {
        max-height: 400px;
        overflow-y: auto;
    }

    .modern-table {
        margin: 0;
        border: none;
    }

    .modern-table thead th {
        background-color: var(--light);
        border: none;
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.875rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .modern-table tbody tr {
        border: none;
        transition: background-color 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background-color: var(--light);
    }

    .modern-table tbody td {
        padding: 1rem;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .customer-name {
        font-weight: 600;
        color: var(--dark);
    }

    .customer-email {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .product-list {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .amount-cell {
        font-weight: 600;
        color: var(--dark);
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-pending {
        background-color: var(--warning-light);
        color: var(--warning);
    }

    .status-processing {
        background-color: var(--info-light);
        color: var(--info);
    }

    .status-shipped {
        background-color: var(--success-light);
        color: var(--success);
    }

    .status-delivered {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .status-cancelled {
        background-color: var(--danger-light);
        color: var(--danger);
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: var(--secondary);
    }

    .no-data i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .btn-view-all {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        background-color: var(--primary);
        color: white;
        border: 1px solid var(--primary);
        transition: all 0.2s ease;
    }

    .btn-view-all:hover {
        background-color: transparent;
        color: var(--primary);
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-header {
            padding: 1.5rem;
        }

        .dashboard-header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .loading-spinner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .loading-spinner.active {
        opacity: 1;
        visibility: visible;
    }

    .spinner {
        width: 3rem;
        height: 3rem;
        border: 3px solid var(--border);
        border-top: 3px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

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

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header animate-fade-in">
        <h1>Tableau de bord des commandes</h1>
        <p>Vue d'ensemble des performances, statistiques en temps réel et analyse des tendances</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card total-orders animate-fade-in" style="animation-delay: 0.1s;">
            <div class="stat-card-header">
                <div class="stat-icon blue">
                    <i class="bx bx-shopping-bag"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['totalOrders']) }}</div>
            <div class="stat-label">Commandes totales</div>
            <div class="stat-change positive">
                <i class="bx bx-trending-up"></i>
                <span>+12% ce mois</span>
            </div>
        </div>

        <div class="stat-card total-sales animate-fade-in" style="animation-delay: 0.2s;">
            <div class="stat-card-header">
                <div class="stat-icon green">
                    <i class="bx bx-dollar-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['totalSales'], 0, ',', ' ') }}</div>
            <div class="stat-label">Chiffre d'affaires (FCFA)</div>
            <div class="stat-change positive">
                <i class="bx bx-trending-up"></i>
                <span>+8% ce mois</span>
            </div>
        </div>

        <div class="stat-card pending-orders animate-fade-in" style="animation-delay: 0.3s;">
            <div class="stat-card-header">
                <div class="stat-icon orange">
                    <i class="bx bx-time"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['pendingOrders'] }}</div>
            <div class="stat-label">En attente</div>
            <div class="stat-change negative">
                <i class="bx bx-trending-down"></i>
                <span>-3% cette semaine</span>
            </div>
        </div>

        <div class="stat-card priority-orders animate-fade-in" style="animation-delay: 0.4s;">
            <div class="stat-card-header">
                <div class="stat-icon purple">
                    <i class="bx bx-bell"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['priorityOrders'] }}</div>
            <div class="stat-label">Prioritaires</div>
            <div class="stat-change positive">
                <i class="bx bx-trending-up"></i>
                <span>+5% cette semaine</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Line Chart -->
        <div class="chart-card animate-fade-in" style="animation-delay: 0.5s;">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Évolution des commandes</div>
                    <div class="chart-subtitle">Tendance mensuelle de {{ date('Y') }}</div>
                </div>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="chart-card animate-fade-in" style="animation-delay: 0.6s;">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Répartition par statut</div>
                    <div class="chart-subtitle">Distribution actuelle</div>
                </div>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders-card animate-fade-in" style="animation-delay: 0.7s;">
        <div class="card-header-custom">
            <div>
                <div class="chart-title">Commandes récentes</div>
                <div class="chart-subtitle">Dernières activités</div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn-view-all">
                Voir toutes
            </a>
        </div>

        <div class="table-container">
            @if ($recentOrders->isNotEmpty())
                <table class="table modern-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Produits</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                        <tr>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-name">{{ $order->customer_name }}</div>
                                    <div class="customer-email">{{ $order->customer_email ?: 'Non renseigné' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="product-list" title="{{ $order->items->pluck('product.name')->join(', ') }}">
                                    {{ $order->items->count() }} produit(s)
                                </div>
                            </td>
                            <td class="amount-cell">
                                {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="color: var(--secondary); font-size: 0.875rem;">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                                    <div style="font-size: 0.75rem; opacity: 0.7;">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <i class="bx bx-info-circle"></i>
                    <div>Aucune commande récente pour le moment</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div class="loading-spinner" id="loadingSpinner">
    <div class="spinner"></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Configuration des graphiques
    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#64748b';

    // Graphique en ligne des commandes
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Commandes',
                data: chartData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#334155',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.raw} commande${context.raw > 1 ? 's' : ''}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
                    }
                }
            }
        }
    });

    // Graphique circulaire des statuts
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    const pieData = @json($statusPieChartData);

    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: pieData.labels,
            datasets: [{
                data: pieData.data,
                backgroundColor: pieData.colors,
                borderColor: '#ffffff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#334155',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Animation d'entrée
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fade-in').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush
