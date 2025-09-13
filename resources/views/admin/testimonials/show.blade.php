@extends('admin.layouts.app')

@section('title', 'Détails du Témoignage')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Témoignages /</span> {{ $testimonial->client_name }}
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails du Témoignage</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center mb-4 p-4 bg-light rounded">
                                    <i class="bx bxs-quote-alt-left display-3 text-primary mb-3"></i>
                                    <h3 class="mb-2">{{ $testimonial->client_name }}</h3>
                                    <p class="text-muted mb-1">{{ $testimonial->client_position }} - {{ $testimonial->client_company }}</p>
                                    <div class="star-rating mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $testimonial->rating)
                                                <i class="bx bxs-star text-warning"></i>
                                            @else
                                                <i class="bx bx-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="badge bg-{{ $testimonial->published ? 'success' : 'warning' }} mb-2">
                                        {{ $testimonial->published ? 'Publié' : 'Brouillon' }}
                                    </span>
                                    @if($testimonial->featured)
                                        <span class="badge bg-info">À la une</span>
                                    @endif
                                    @if($testimonial->project_name)
                                        <div>
                                            <small class="text-muted">Projet: {{ $testimonial->project_name }}</small>
                                        </div>
                                    @endif
                                    @if($testimonial->date)
                                        <div>
                                            <small class="text-muted">Date: {{ \Carbon\Carbon::parse($testimonial->date)->format('d/m/Y') }}</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <h6 class="mb-2">Contenu du témoignage</h6>
                                    <p class="text-muted">{{ $testimonial->content }}</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Informations</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Client:</strong> {{ $testimonial->client_name }}</li>
                                            <li><strong>Poste:</strong> {{ $testimonial->client_position ?? 'Non défini' }}</li>
                                            <li><strong>Entreprise:</strong> {{ $testimonial->client_company ?? 'Non défini' }}</li>
                                            <li><strong>Note:</strong> {{ $testimonial->rating }} / 5</li>
                                            <li><strong>Publié:</strong> {{ $testimonial->published ? 'Oui' : 'Non' }}</li>
                                            <li><strong>À la une:</strong> {{ $testimonial->featured ? 'Oui' : 'Non' }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Dates</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Créé le:</strong> {{ $testimonial->created_at?->format('d/m/Y à H:i') }}</li>
                                            <li><strong>Modifié le:</strong> {{ $testimonial->updated_at?->format('d/m/Y à H:i') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i>Modifier ce témoignage
                            </a>
                            @if($testimonial->published)
                                <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="published" value="0">
                                    <input type="hidden" name="client_name" value="{{ $testimonial->client_name }}">
                                    <input type="hidden" name="client_position" value="{{ $testimonial->client_position }}">
                                    <input type="hidden" name="client_company" value="{{ $testimonial->client_company }}">
                                    <input type="hidden" name="content" value="{{ $testimonial->content }}">
                                    <input type="hidden" name="rating" value="{{ $testimonial->rating }}">
                                    <input type="hidden" name="project_name" value="{{ $testimonial->project_name }}">
                                    <input type="hidden" name="date" value="{{ $testimonial->date }}">
                                    <input type="hidden" name="featured" value="{{ $testimonial->featured }}">
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="bx bx-hide me-1"></i>Dépublier
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="published" value="1">
                                    <input type="hidden" name="client_name" value="{{ $testimonial->client_name }}">
                                    <input type="hidden" name="client_position" value="{{ $testimonial->client_position }}">
                                    <input type="hidden" name="client_company" value="{{ $testimonial->client_company }}">
                                    <input type="hidden" name="content" value="{{ $testimonial->content }}">
                                    <input type="hidden" name="rating" value="{{ $testimonial->rating }}">
                                    <input type="hidden" name="project_name" value="{{ $testimonial->project_name }}">
                                    <input type="hidden" name="date" value="{{ $testimonial->date }}">
                                    <input type="hidden" name="featured" value="{{ $testimonial->featured }}">
                                    <button type="submit" class="btn btn-outline-success w-100">
                                        <i class="bx bx-show me-1"></i>Publier
                                    </button>
                                </form>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce témoignage ?')">
                                    <i class="bx bx-trash me-1"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-1 text-warning">{{ $testimonial->rating }} / 5</h4>
                                    <small class="text-muted">Note</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1 text-{{ $testimonial->published ? 'success' : 'warning' }}">
                                    {{ $testimonial->published ? 'Oui' : 'Non' }}
                                </h4>
                                <small class="text-muted">Publié</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Navigation</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-list-ul me-1"></i>Tous les témoignages
                            </a>
                            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Nouveau témoignage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
