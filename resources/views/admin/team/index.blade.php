@extends('admin.layouts.app')

@section('title', 'Gestion des Membres de l\'Équipe')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Membres de l'équipe
        </h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Membres</h5>
                <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Nouveau Membre
                </a>
            </div>

            <div class="card-body">
                @if($teamMembers->count() > 0)
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Poste</th>
                                    <th>Ordre</th>
                                    <th>Publié</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($teamMembers as $teamMember)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <img src="{{ $teamMember->photo ? asset($teamMember->photo) : asset('assets/img/avatars/user_placeholder.png') }}" alt="Photo de {{ $teamMember->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>{{ $teamMember->name }}</strong></td>
                                    <td>{{ $teamMember->position }}</td>
                                    <td>{{ $teamMember->order ?? 0 }}</td>
                                    <td>
                                        <span class="badge {{ $teamMember->published ? 'bg-label-success' : 'bg-label-warning' }}">
                                            {{ $teamMember->published ? 'Oui' : 'Non' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.team.show', $teamMember) }}"><i class="bx bx-show-alt me-1"></i> Voir</a>
                                                <a class="dropdown-item" href="{{ route('admin.team.edit', $teamMember) }}"><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteModal" data-member-id="{{ $teamMember->id }}" data-member-name="{{ $teamMember->name }}">
                                                    <i class="bx bx-trash me-1"></i> Supprimer
                                                </a>
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
                        <i class="bx bx-group display-4 text-muted"></i>
                        <h5 class="mt-3">Aucun membre d'équipe trouvé</h5>
                        <p class="text-muted">Commencez par ajouter votre premier membre de l'équipe</p>
                        <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Créer un membre
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le membre <span id="memberName" class="fw-bold"></span> ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const memberNameSpan = document.getElementById('memberName');

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const memberId = button.getAttribute('data-member-id');
        const memberName = button.getAttribute('data-member-name');

        memberNameSpan.textContent = memberName;
        deleteForm.action = "{{ route('admin.team.destroy', ':id') }}".replace(':id', memberId);
    });
});
</script>
@endsection
