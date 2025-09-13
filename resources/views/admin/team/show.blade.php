@extends('admin.layouts.app')

@section('title', 'Détails du Membre de l\'équipe')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Membres de l'équipe /</span> {{ $teamMember->name }}
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails du Membre</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.team.edit', $teamMember) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center mb-4 p-4 bg-light rounded">
                                    @if($teamMember->photo)
                                        <img src="{{ asset($teamMember->photo) }}" alt="Photo de {{ $teamMember->name }}" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/avatars/user_placeholder.png') }}" alt="Photo de profil par défaut" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                    @endif
                                    <h4 class="fw-semibold mb-1">{{ $teamMember->name }}</h4>
                                    <p class="text-muted">{{ $teamMember->position }}</p>
                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                        @if ($teamMember->linkedin)
                                            <a href="{{ $teamMember->linkedin }}" target="_blank" class="btn btn-icon btn-label-linkedin"><i class="bx bxl-linkedin"></i></a>
                                        @endif
                                        @if ($teamMember->twitter)
                                            <a href="{{ $teamMember->twitter }}" target="_blank" class="btn btn-icon btn-label-twitter"><i class="bx bxl-twitter"></i></a>
                                        @endif
                                        @if ($teamMember->facebook)
                                            <a href="{{ $teamMember->facebook }}" target="_blank" class="btn btn-icon btn-label-facebook"><i class="bx bxl-facebook"></i></a>
                                        @endif
                                        @if ($teamMember->instagram)
                                            <a href="{{ $teamMember->instagram }}" target="_blank" class="btn btn-icon btn-label-instagram"><i class="bx bxl-instagram"></i></a>
                                        @endif
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase mt-4 mb-3">Informations détaillées</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Biographie :</th>
                                                <td>{{ $teamMember->bio ?? 'Non spécifié' }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Compétences :</th>
                                                <td>
                                                    @if ($teamMember->skills)
                                                        @foreach(explode(',', $teamMember->skills) as $skill)
                                                            <span class="badge bg-label-info me-1">{{ trim($skill) }}</span>
                                                        @endforeach
                                                    @else
                                                        Aucune compétence spécifiée.
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Expérience :</th>
                                                <td>{{ $teamMember->experience_text }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Ordre :</th>
                                                <td>{{ $teamMember->order ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Publié :</th>
                                                <td>
                                                    <span class="badge {{ $teamMember->published ? 'bg-label-success' : 'bg-label-warning' }}">
                                                        {{ $teamMember->published ? 'Oui' : 'Non' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Créé le :</th>
                                                <td>{{ $teamMember->created_at->translatedFormat('d F Y à H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Actions Rapides</h5></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.team.edit', $teamMember) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('admin.team.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Nouveau membre
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bx bx-trash me-1"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Navigation</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-list-ul me-1"></i>Tous les membres
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de Suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce membre ? Cette action est irréversible et toutes les données associées seront perdues.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="{{ route('admin.team.destroy', $teamMember) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
