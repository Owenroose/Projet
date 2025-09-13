@extends('admin.layouts.app')

@section('title', 'Gestion des Témoignages')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Témoignages
        </h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Témoignages</h5>
                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Nouveau Témoignage
                </a>
            </div>

            <div class="card-body">
                @if($testimonials->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Entreprise / Poste</th>
                                    <th>Contenu</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        <strong>{{ $testimonial->client_name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $testimonial->client_company }}</small><br>
                                        {{ $testimonial->client_position }}
                                    </td>
                                    <td>
                                        <span class="text-truncate d-block" style="max-width: 300px;">
                                            {{ Str::limit($testimonial->content, 100) }}
                                        </span>
                                    </td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $testimonial->rating)
                                                <i class="bx bxs-star text-warning"></i>
                                            @else
                                                <i class="bx bx-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>
                                        {{ $testimonial->date ? \Carbon\Carbon::parse($testimonial->date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($testimonial->published)
                                            <span class="badge bg-success">Publié</span>
                                        @else
                                            <span class="badge bg-warning">Brouillon</span>
                                        @endif
                                        @if($testimonial->featured)
                                            <span class="badge bg-info">À la une</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.testimonials.show', $testimonial) }}">
                                                    <i class="bx bx-show me-1"></i> Voir
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.testimonials.edit', $testimonial) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Modifier
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce témoignage ?')">
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
                        <i class="bx bxs-quote-alt-left display-4 text-muted"></i>
                        <h5 class="mt-3">Aucun témoignage trouvé</h5>
                        <p class="text-muted">Commencez par ajouter votre premier témoignage</p>
                        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Ajouter un témoignage
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
