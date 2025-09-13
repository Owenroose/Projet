@extends('admin.layouts.app')

@section('title', 'Modifier le rôle')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bx bx-arrow-back me-1"></i>
                Retour
            </a>
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-shield-quarter text-primary me-2"></i>
                    Modifier le Rôle
                </h4>
                <p class="text-muted mb-0">Modifier les informations et les permissions de ce rôle</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">
                <i class="bx bx-edit-alt me-2 text-warning"></i>
                Informations du Rôle : {{ $role->name }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nom du rôle</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="Nom du rôle" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">
                <i class="bx bx-key me-2 text-primary"></i>
                Permissions
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach ($permissions->groupBy(fn($p) => explode('-', $p->name)[1]) as $module => $modulePermissions)
                <div class="col-md-4">
                    <div class="card card-body h-100 border-0 bg-light shadow-sm">
                        <h6 class="card-title text-capitalize fw-bold">{{ $module }}</h6>
                        @foreach ($modulePermissions as $permission)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission-{{ $permission->id }}" @checked(in_array($permission->name, $rolePermissions))>
                            <label class="form-check-label text-muted" for="permission-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer bg-white border-top text-end">
            <button type="submit" class="btn btn-warning">
                <i class="bx bx-edit-alt me-1"></i>
                Mettre à jour le rôle
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-x me-1"></i>
                Annuler
            </a>
        </div>
    </div>
</div>
@endsection
