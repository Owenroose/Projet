@extends('admin.layouts.app')

@section('title', 'Créer un Produit')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Produits /</span> Nouveau Produit
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informations du Produit</h5>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>Retour
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Entrez le nom du produit" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5" placeholder="Description du produit" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="specifications" class="form-label">Spécifications</label>
                                <textarea class="form-control @error('specifications') is-invalid @enderror"
                                          id="specifications" name="specifications" rows="4" placeholder="Entrez les spécifications, ex: 'Couleur: Noir' ou 'RAM: 8 Go'">{{ old('specifications') }}</textarea>
                                @error('specifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Prix (CFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="brand" class="form-label">Marque</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                           id="brand" name="brand" value="{{ old('brand') }}" placeholder="Ex: HP">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                       id="category" name="category" value="{{ old('category') }}" placeholder="Ex: Ordinateurs" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image du produit <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="in_stock" name="in_stock" value="1" {{ old('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="in_stock">En stock</label>
                            </div>

                            <div class="mb-3" id="stock-quantity-container" style="display: {{ old('in_stock') ? 'block' : 'none' }};">
                                <label for="stock_quantity" class="form-label">Quantité en stock</label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" min="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="published" name="published" value="1" {{ old('published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="published">Publier le produit</label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Créer le produit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Aide</h5></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class="bx bx-image text-info me-1"></i>
                                La taille maximale de l'image est de 2 Mo.
                            </li>
                            <li>
                                <i class="bx bx-checkbox-checked text-info me-1"></i>
                                Seuls les produits publiés sont visibles pour les clients.
                            </li>
                            <li>
                                <i class="bx bx-info-circle text-info me-1"></i>
                                Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inStockCheckbox = document.getElementById('in_stock');
        const stockQuantityContainer = document.getElementById('stock-quantity-container');

        inStockCheckbox.addEventListener('change', function() {
            stockQuantityContainer.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endsection
