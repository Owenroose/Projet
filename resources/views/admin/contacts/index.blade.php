@extends('admin.layouts.app')

@section('title', 'Gestion des Messages de Contact')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Messages de Contact
        </h4>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Messages</h5>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-refresh me-1"></i>Actualiser
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.contacts.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tous</option>
                                @foreach(App\Models\Contact::getStatusOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priorité</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>Toutes</option>
                                @foreach(App\Models\Contact::getPriorityOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="read" class="form-label">Lecture</label>
                            <select class="form-select" id="read" name="read">
                                <option value="all" {{ request('read') == 'all' ? 'selected' : '' }}>Tous</option>
                                <option value="unread" {{ request('read') == 'unread' ? 'selected' : '' }}>Non lus</option>
                                <option value="read" {{ request('read') == 'read' ? 'selected' : '' }}>Lus</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter-alt me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                        </div>
                    </div>
                </form>

                @if($contacts->count() > 0)
                    <form id="bulk-action-form" action="{{ route('admin.contacts.bulk-action') }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-2">
                                <select class="form-select" name="action" id="bulk-action">
                                    <option value="">Actions de masse</option>
                                    <option value="read">Marquer comme lu</option>
                                    <option value="unread">Marquer comme non lu</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info btn-sm" id="apply-bulk-action">Appliquer</button>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Sujet</th>
                                        <th>De</th>
                                        <th>Statut</th>
                                        <th>Priorité</th>
                                        <th>Lu</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                    <tr>
                                        <td><input type="checkbox" class="contact-checkbox" name="ids[]" value="{{ $contact->id }}"></td>
                                        <td>
                                            <a href="{{ route('admin.contacts.show', $contact) }}" class="fw-bold text-primary">{{ Str::limit($contact->subject, 40) }}</a>
                                        </td>
                                        <td>{{ $contact->name }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'new' => 'info',
                                                    'in_progress' => 'primary',
                                                    'resolved' => 'success',
                                                    'closed' => 'secondary'
                                                ][$contact->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ App\Models\Contact::getStatusOptions()[$contact->status] }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $priorityClass = [
                                                    'low' => 'success',
                                                    'medium' => 'info',
                                                    'high' => 'warning',
                                                    'urgent' => 'danger',
                                                ][$contact->priority] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $priorityClass }}">{{ App\Models\Contact::getPriorityOptions()[$contact->priority] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $contact->read ? 'success' : 'danger' }}">{{ $contact->read ? 'Oui' : 'Non' }}</span>
                                        </td>
                                        <td>{{ $contact->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-sm btn-icon btn-label-secondary">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');" class="d-inline">
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
                    </form>

                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-envelope-open display-4 text-muted"></i>
                        <h5 class="mt-3">Aucun message de contact trouvé</h5>
                        <p class="text-muted">Il n'y a pas encore de messages de contact à afficher</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkActionForm = document.getElementById('bulk-action-form');
    const applyBulkActionBtn = document.getElementById('apply-bulk-action');

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    applyBulkActionBtn.addEventListener('click', function(e) {
        const action = document.getElementById('bulk-action').value;
        if (!action) {
            e.preventDefault();
            alert("Veuillez sélectionner une action.");
            return;
        }

        const selectedIds = Array.from(contactCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert("Veuillez sélectionner au moins un message.");
            return;
        }

        if (action === 'delete') {
            if (!confirm("Êtes-vous sûr de vouloir supprimer les messages sélectionnés ? Cette action est irréversible.")) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
