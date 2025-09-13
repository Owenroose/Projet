@extends('admin.layouts.app')

@section('title', 'Détails du Produit')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Produits /</span> {{ $product->name }}
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails du Produit</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center mb-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Image du produit" class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                                @endif
                            </div>
                            <div class="col-md-12">
                                <h3 class="mb-2">{{ $product->name }}</h3>
                                <p><strong>Marque:</strong> {{ $product->brand ?? 'N/A' }}</p>
                                <p><strong>Catégorie:</strong> {{ $product->category }}</p>
                                <p><strong>Prix:</strong> {{ number_format($product->price, 2, ',', '.') }} CFA</p>

                                <h5 class="mt-4">Description</h5>
                                <p>{{ $product->description }}</p>

                                @if($product->specifications)
                                    <h5 class="mt-4">Spécifications</h5>
                                    <ul class="list-group list-group-flush">
                                        @foreach($product->specificationsArray as $key => $value)
                                            <li class="list-group-item">{{ $key }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <hr class="my-4">

                                <p><strong>En stock:</strong>
                                    <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">{{ $product->in_stock ? 'Oui' : 'Non' }}</span>
                                    @if($product->in_stock && $product->stock_quantity)
                                        ({{ $product->stock_quantity }} disponibles)
                                    @endif
                                </p>
                                <p><strong>Publié:</strong>
                                    <span class="badge bg-{{ $product->published ? 'success' : 'secondary' }}">{{ $product->published ? 'Oui' : 'Non' }}</span>
                                </p>
                                <p><strong>Créé le:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Dernière mise à jour:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Navigation</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-list-ul me-1"></i>Tous les produits
                            </a>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Nouveau produit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
