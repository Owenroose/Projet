@extends('admin.layouts.app')

@section('title', 'Modifier le Service')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Services /</span> Modifier "{{ $service->name }}"
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Modifier le Service</h5>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>Retour
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- Ajout de l'attribut enctype pour l'upload de fichiers --}}
                        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du service <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $service->name) }}" placeholder="Entrez le nom du service" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image du service</label>
                                @if($service->image)
                                    <div class="mb-2">
                                        <img src="{{ asset($service->image) }}" alt="Image actuelle" style="max-width: 200px; border-radius: 8px;">
                                    </div>
                                @endif
                                <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image">
                                <small class="text-muted">Laissez vide pour conserver l'image actuelle.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3" placeholder="Description du service" required>{{ old('description', $service->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="features" class="form-label">Fonctionnalités (séparées par un |)</label>
                                <input type="text" class="form-control @error('features') is-invalid @enderror"
                                       id="features" name="features" value="{{ old('features', $service->features) }}" placeholder="Ex: Support 24/7 | Mises à jour | Correction de bugs">
                                @error('features')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="icon" class="form-label">Icône Font Awesome ou Boxicons</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                       id="icon" name="icon" value="{{ old('icon', $service->icon) }}" placeholder="Ex: fas fa-cog ou bx bx-wrench">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="order" class="form-label">Ordre d'affichage</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror"
                                       id="order" name="order" value="{{ old('order', $service->order) }}">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="published" id="published" value="1" {{ old('published', $service->published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="published">Publier le service</label>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Mettre à jour le service</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Actions</h5></div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('admin.services.create') }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-plus me-1"></i>Nouveau service
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?')">
                                    <i class="bx bx-trash me-1"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
