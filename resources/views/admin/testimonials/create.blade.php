@extends('admin.layouts.app')

@section('title', 'Créer un Témoignage')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Témoignages /</span> Nouveau Témoignage
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informations du Témoignage</h5>
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>Retour
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.testimonials.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="client_name" class="form-label">Nom du client <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                       id="client_name" name="client_name" value="{{ old('client_name') }}"
                                       placeholder="Ex: Jean Dupont">
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="client_position" class="form-label">Poste du client</label>
                                <input type="text" class="form-control @error('client_position') is-invalid @enderror"
                                       id="client_position" name="client_position" value="{{ old('client_position') }}"
                                       placeholder="Ex: Directeur Marketing">
                                @error('client_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="client_company" class="form-label">Entreprise du client</label>
                                <input type="text" class="form-control @error('client_company') is-invalid @enderror"
                                       id="client_company" name="client_company" value="{{ old('client_company') }}"
                                       placeholder="Ex: Ma Super Agence">
                                @error('client_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror"
                                          id="content" name="content" rows="4"
                                          placeholder="Contenu du témoignage">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="rating" class="form-label">Note <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('rating') is-invalid @enderror"
                                       id="rating" name="rating" value="{{ old('rating') }}" min="1" max="5">
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="project_name" class="form-label">Nom du projet</label>
                                <input type="text" class="form-control @error('project_name') is-invalid @enderror"
                                       id="project_name" name="project_name" value="{{ old('project_name') }}"
                                       placeholder="Ex: Site Web d'entreprise">
                                @error('project_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Date du témoignage</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                       id="date" name="date" value="{{ old('date') }}">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="published"
                                           name="published" {{ old('published') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="published">
                                        Publier le témoignage
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured"
                                           name="featured" {{ old('featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Mettre en avant
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>Créer le témoignage
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Aperçu</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="testimonial-preview">
                            <i class="bx bxs-quote-alt-left display-4 text-primary mb-3"></i>
                            <p class="text-muted" id="content-preview">Contenu du témoignage...</p>
                            <div class="star-rating mb-2" id="rating-preview">
                                <i class="bx bx-star text-warning"></i>
                                <i class="bx bx-star text-warning"></i>
                                <i class="bx bx-star text-warning"></i>
                                <i class="bx bx-star text-warning"></i>
                                <i class="bx bx-star text-warning"></i>
                            </div>
                            <h5 class="mb-0" id="name-preview">Nom du client</h5>
                            <small class="text-muted" id="position-preview">Poste du client</small>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Aide</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bx bx-info-circle text-info me-1"></i>
                                La note doit être entre 1 et 5
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-eye text-info me-1"></i>
                                Seuls les témoignages publiés sont visibles sur le site
                            </li>
                            <li>
                                <i class="bx bx-star text-info me-1"></i>
                                Les témoignages "à la une" peuvent être mis en avant
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
    // Prévisualisation en temps réel
    const nameInput = document.getElementById('client_name');
    const positionInput = document.getElementById('client_position');
    const contentInput = document.getElementById('content');
    const ratingInput = document.getElementById('rating');

    const namePreview = document.getElementById('name-preview');
    const positionPreview = document.getElementById('position-preview');
    const contentPreview = document.getElementById('content-preview');
    const ratingPreview = document.getElementById('rating-preview');

    nameInput.addEventListener('input', function() {
        namePreview.textContent = this.value || 'Nom du client';
    });

    positionInput.addEventListener('input', function() {
        positionPreview.textContent = this.value || 'Poste du client';
    });

    contentInput.addEventListener('input', function() {
        contentPreview.textContent = this.value || 'Contenu du témoignage...';
    });

    ratingInput.addEventListener('input', function() {
        const rating = this.value > 5 ? 5 : (this.value < 1 ? 0 : this.value);
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                starsHtml += '<i class="bx bxs-star text-warning"></i>';
            } else {
                starsHtml += '<i class="bx bx-star text-warning"></i>';
            }
        }
        ratingPreview.innerHTML = starsHtml;
    });
});
</script>
@endsection
