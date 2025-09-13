@extends('admin.layouts.app')

@section('title', 'Gestion des Services')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Services
        </h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Services</h5>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Nouveau Service
                </a>
            </div>

            <div class="card-body">
                @if($services->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ordre</th>
                                    <th>Nom</th>
                                    <th>Visibilité</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->order }}</td>
                                    <td>
                                        <i class="bx {{ $service->icon }} me-2"></i>
                                        <strong>{{ $service->name }}</strong>
                                    </td>
                                    <td>
                                        @if($service->published)
                                            <span class="badge bg-label-success me-1">Publié</span>
                                        @else
                                            <span class="badge bg-label-warning me-1">Brouillon</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-icon btn-label-info me-1">
                                                <i class="bx bx-show-alt"></i>
                                            </a>
                                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-icon btn-label-secondary me-1">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
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
                    <div class="text-center py-5">
                        <i class="bx bx-service-icon display-4 text-muted"></i>
                        <h5 class="mt-3">Aucun service trouvé</h5>
                        <p class="text-muted">Commencez par créer votre premier service</p>
                        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Créer un service
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
