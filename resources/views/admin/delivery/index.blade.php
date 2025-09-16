@extends('admin.layouts.app')

@section('title', 'Mes livraisons')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Mes livraisons</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th># Commande</th>
                                    <th>Statut</th>
                                    <th>Client</th>
                                    <th>Adresse</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveries as $delivery)
                                    <tr>
                                        <td>{{ substr($delivery->order_group, 0, 8) }}</td>
                                        <td>{{ $delivery->status }}</td>
                                        <td>{{ $delivery->orders->first()->customer_name }}</td>
                                        <td>{{ $delivery->orders->first()->customer_address }}</td>
                                        <td>{{ number_format($delivery->orders->first()->total_amount, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @if($delivery->status === 'assigned' || $delivery->status === 'in_delivery')
                                                <form action="{{ route('delivery.validate', $delivery->order_group) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        Valider la livraison
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success">Livrée</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune livraison affectée pour le moment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
