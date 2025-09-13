@extends('admin.layouts.app')

@section('title', 'Gestion des Produits')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Produits
        </h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Produits</h5>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Nouveau Produit
                </a>
            </div>

            <div class="card-body">
                @if($products->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($products as $product)
                                <tr>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 50px; height: auto;">
                                        @else
                                            <div class="p-2 border rounded d-inline-block"><i class="bx bx-image-alt fs-3 text-muted"></i></div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->category }}</td>
                                    <td>{{ number_format($product->price, 2, ',', '.') }} CFA</td>
                                    <td>
                                        @if($product->in_stock)
                                            <span class="badge bg-success">En stock</span>
                                            @if($product->stock_quantity)
                                                ({{ $product->stock_quantity }})
                                            @endif
                                        @else
                                            <span class="badge bg-danger">Rupture</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->published)
                                            <span class="badge bg-success">Publié</span>
                                        @else
                                            <span class="badge bg-warning">Brouillon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.products.show', $product) }}"><i class="bx bx-show me-1"></i> Voir</a>
                                                <a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}"><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-1"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-package display-4 text-muted"></i>
                        <h5 class="mt-3">Aucun produit trouvé</h5>
                        <p class="text-muted">Commencez par créer votre premier produit</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Créer un produit
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
