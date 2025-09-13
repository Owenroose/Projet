@extends('admin.layouts.app')

@section('title', 'Gestion des Projets')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration /</span> Projets
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-label-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary p-2 rounded me-3"><i class="bx bx-list-ul fs-4"></i></span>
                    <div>
                        <h5 class="mb-0">{{ $projects->count() }}</h5>
                        <small>Total de Projets</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-label-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="badge bg-success p-2 rounded me-3"><i class="bx bx-check-circle fs-4"></i></span>
                    <div>
                        <h5 class="mb-0">{{ $projects->where('published', true)->count() }}</h5>
                        <small>Projets Publiés</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-label-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="badge bg-info p-2 rounded me-3"><i class="bx bx-star fs-4"></i></span>
                    <div>
                        <h5 class="mb-0">{{ $projects->where('featured', true)->count() }}</h5>
                        <small>Projets en Vedette</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-label-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning p-2 rounded me-3"><i class="bx bx-file-blank fs-4"></i></span>
                    <div>
                        <h5 class="mb-0">{{ $projects->where('published', false)->count() }}</h5>
                        <small>Brouillons</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Projets</h5>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Nouveau Projet
        </a>
    </div>

    <div class="card-body">
        @if($projects->count() > 0)
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Image</th>
                        <th>Titre</th>
                        <th>Client</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($projects as $project)
                    <tr>
                        <td>
                            @if($project->image)
                            <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="img-fluid rounded" style="width: 80px; height: 60px; object-fit: cover;">
                            @else
                            <div class="p-2 border rounded d-inline-block"><i class="bx bx-image-alt fs-3 text-muted"></i></div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $project->title }}</strong><br>
                            <small class="text-muted">{{ $project->client }}</small>
                        </td>
                        <td>{{ $project->client }}</td>
                        <td class="text-center">
                            @if($project->published)
                            <span class="badge bg-success">Publié</span>
                            @else
                            <span class="badge bg-warning">Brouillon</span>
                            @endif
                            @if($project->featured)
                            <span class="badge bg-info ms-1">En vedette</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-icon btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info mb-0 text-center">Aucun projet n'a été trouvé. Créez-en un nouveau pour commencer !</div>
        @endif
    </div>
</div>
@endsection
