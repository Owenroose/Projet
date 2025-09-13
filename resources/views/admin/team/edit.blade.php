@extends('admin.layouts.app')

@section('title', 'Modifier le Membre de l\'Équipe')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Membres de l'équipe /</span> Modifier "{{ $teamMember->name }}"
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Modifier le Membre</h5>
                        <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>Retour
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.team.update', $teamMember) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du membre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $teamMember->name) }}" placeholder="Ex: Jean Dupont">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="position" class="form-label">Poste <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                       id="position" name="position" value="{{ old('position', $teamMember->position) }}" placeholder="Ex: Développeur Senior">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Biographie</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                          id="bio" name="bio" rows="3" placeholder="Courte biographie du membre">{{ old('bio', $teamMember->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                @if($teamMember->photo)
                                    <div class="mb-2">
                                        <img src="{{ asset($teamMember->photo) }}" alt="Photo actuelle" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                                <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo" name="photo">
                                <small class="text-muted">Laissez vide pour conserver la photo actuelle.</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="linkedin" class="form-label"><i class="bx bxl-linkedin me-1"></i>LinkedIn</label>
                                    <input type="url" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" name="linkedin" value="{{ old('linkedin', $teamMember->linkedin) }}">
                                    @error('linkedin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="twitter" class="form-label"><i class="bx bxl-twitter me-1"></i>Twitter</label>
                                    <input type="url" class="form-control @error('twitter') is-invalid @enderror" id="twitter" name="twitter" value="{{ old('twitter', $teamMember->twitter) }}">
                                    @error('twitter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="facebook" class="form-label"><i class="bx bxl-facebook me-1"></i>Facebook</label>
                                    <input type="url" class="form-control @error('facebook') is-invalid @enderror" id="facebook" name="facebook" value="{{ old('facebook', $teamMember->facebook) }}">
                                    @error('facebook')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="instagram" class="form-label"><i class="bx bxl-instagram me-1"></i>Instagram</label>
                                    <input type="url" class="form-control @error('instagram') is-invalid @enderror" id="instagram" name="instagram" value="{{ old('instagram', $teamMember->instagram) }}">
                                    @error('instagram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="order" class="form-label">Ordre d'affichage</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror"
                                       id="order" name="order" value="{{ old('order', $teamMember->order) }}">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="published" id="published" value="1" {{ old('published', $teamMember->published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="published">Publier le membre</label>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Mettre à jour le membre</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Aperçu et Actions</h5></div>
                    <div class="card-body text-center">
                        <img id="photo-preview" src="{{ $teamMember->photo ? asset($teamMember->photo) : asset('assets/img/avatars/user_placeholder.png') }}" alt="Aperçu de la photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4 class="mb-1">{{ $teamMember->name }}</h4>
                        <p class="text-muted mb-2">{{ $teamMember->position }}</p>
                        <small class="text-muted d-block px-4">{{ Str::limit($teamMember->bio, 100) }}</small>

                        <div class="mt-4 d-flex justify-content-center gap-2">
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
                </div>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Navigation</h5></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.team.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Nouveau membre
                            </a>
                            <form action="{{ route('admin.team.destroy', $teamMember) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')">
                                    <i class="bx bx-trash me-1"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            photoPreview.src = '{{ $teamMember->photo ? asset($teamMember->photo) : asset('assets/img/avatars/user_placeholder.png') }}';
        }
    });
});
</script>
@endsection
