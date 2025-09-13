@extends('admin.layouts.app')

@section('title', 'Détails du Projet')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration / Projets /</span> {{ $project->title }}
</h4>

<div class="row">
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Détails du projet</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-edit me-1"></i>Modifier
                    </a>
                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($project->image)
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="img-fluid rounded shadow" style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif
                <div class="d-flex gap-2 mb-4">
                    @if($project->published)
                        <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Publié</span>
                    @else
                        <span class="badge bg-warning"><i class="bx bx-time me-1"></i>Brouillon</span>
                    @endif
                    @if($project->featured)
                        <span class="badge bg-info"><i class="bx bx-star me-1"></i>En vedette</span>
                    @endif
                </div>

                <h4 class="fw-semibold mt-4 mb-2">Description</h4>
                <p class="text-muted">{{ $project->description }}</p>

                @if($project->challenge)
                    <h4 class="fw-semibold mt-4 mb-2">Défi / Problématique</h4>
                    <p class="text-muted">{{ $project->challenge }}</p>
                @endif

                @if($project->solution)
                    <h4 class="fw-semibold mt-4 mb-2">Solution apportée</h4>
                    <p class="text-muted">{{ $project->solution }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informations clés</h5></div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @if($project->client)
                        <li class="mb-3">
                            <span class="fw-semibold d-block">Client :</span>
                            <span class="text-muted">{{ $project->client }}</span>
                        </li>
                    @endif
                    @if($project->project_date)
                        <li class="mb-3">
                            <span class="fw-semibold d-block">Date du projet :</span>
                            <span class="text-muted">{{ $project->project_date->format('d/m/Y') }}</span>
                        </li>
                    @endif
                    @if($project->project_url)
                        <li class="mb-3">
                            <span class="fw-semibold d-block">URL du projet :</span>
                            <a href="{{ $project->project_url }}" target="_blank" class="text-primary">{{ $project->project_url }}</a>
                        </li>
                    @endif
                    @if($project->technologies)
                        <li class="mb-3">
                            <span class="fw-semibold d-block">Technologies :</span>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(explode(',', $project->technologies) as $tech)
                                    <span class="badge bg-label-secondary">{{ trim($tech) }}</span>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Métadonnées</h5></div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <span class="fw-semibold d-block">Créé le :</span>
                        <span class="text-muted">{{ $project->created_at->format('d/m/Y à H:i') }}</span>
                    </li>
                    <li class="mb-3">
                        <span class="fw-semibold d-block">Dernière mise à jour :</span>
                        <span class="text-muted">{{ $project->updated_at->format('d/m/Y à H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-list-ul me-1"></i>Tous les projets
                    </a>
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary">
                        <i class="bx bx-plus me-1"></i>Nouveau projet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
