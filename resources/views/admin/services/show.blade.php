@extends('admin.layouts.app')

@section('title', 'Détails du Service')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Services /</span> {{ $service->name }}
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails du Service</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center mb-4">
                                    {{-- Affichage conditionnel de l'icône ou de l'image --}}
                                    @if ($service->image)
                                        <img src="{{ asset($service->image) }}" alt="Image du service" class="img-fluid rounded mb-3" style="max-height: 250px;">
                                    @else
                                        <i class="bx {{ $service->icon }} display-1 text-primary mb-3"></i>
                                    @endif

                                    <h4 class="fw-semibold mb-2">{{ $service->name }}</h4>
                                    <span class="badge {{ $service->published ? 'bg-label-success' : 'bg-label-danger' }}">{{ $service->published ? 'Publié' : 'Non publié' }}</span>
                                </div>

                                <h6 class="text-muted text-uppercase mt-4 mb-3">Détails</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Description :</th>
                                                <td>{{ $service->description }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Fonctionnalités :</th>
                                                <td>
                                                    @if ($service->features)
                                                        @foreach(explode('|', $service->features) as $feature)
                                                            <span class="badge bg-label-info me-1">{{ trim($feature) }}</span>
                                                        @endforeach
                                                    @else
                                                        Aucune fonctionnalité spécifiée.
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Icône :</th>
                                                <td><i class="bx {{ $service->icon }} me-1"></i> {{ $service->icon }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Ordre :</th>
                                                <td>{{ $service->order }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Date de création :</th>
                                                <td>{{ $service->created_at->translatedFormat('d F Y à H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="text-nowrap">Dernière mise à jour :</th>
                                                <td>{{ $service->updated_at->translatedFormat('d F Y à H:i') }}</td>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Actions Rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <form action="{{ route('admin.services.togglePublished', $service) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn {{ $service->published ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm w-100">
                                    <i class="bx {{ $service->published ? 'bx-low-vision' : 'bx-show' }} me-1"></i>
                                    {{ $service->published ? 'Dépublier le service' : 'Publier le service' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-edit me-1"></i>Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                <i class="bx bx-trash me-1"></i>Supprimer
                            </button>
                            <a href="{{ route('admin.services.create') }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-plus me-1"></i>Nouveau service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de Suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible et toutes les données associées seront perdues.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
