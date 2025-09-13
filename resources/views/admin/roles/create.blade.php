@extends('admin.layouts.app')

@section('title', 'Créer un rôle')

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
                    Créer un Rôle
                </h4>
                <p class="text-muted mb-0">Définissez les permissions pour ce nouveau rôle</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">
                <i class="bx bx-plus me-2"></i>
                Informations du Rôle
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nom du rôle</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nom du rôle (ex: admin, editor)" required>
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
                <i class="bx bx-key me-2"></i>
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
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission-{{ $permission->id }}">
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
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-check-circle me-1"></i>
                Créer le rôle
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-x me-1"></i>
                Annuler
            </a>
        </div>
    </div>
</div>

<style>
/* Les styles communs peuvent être mis dans le layout principal si nécessaire */
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}
.card-header {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    background-color: #fff;
}
.btn-primary {
    background-color: #696cff;
    border-color: #696cff;
}
.btn-primary:hover {
    background-color: #5557d4;
    border-color: #5557d4;
}
</style>
@endsection
