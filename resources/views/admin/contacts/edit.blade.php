@extends('admin.layouts.app')

@section('title', 'Détails du Message')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration / Messages /</span> {{ Str::limit($contact->subject, 30) }}
        </h4>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails du Message</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <h3 class="mb-2">{{ $contact->subject }}</h3>
                                    <small class="text-muted">
                                        De: <strong>{{ $contact->name }}</strong> ({{ $contact->email }})
                                    </small>
                                    <span class="d-block mt-1">
                                        <i class="bx bx-calendar me-1"></i>
                                        {{ $contact->created_at->format('d/m/Y à H:i') }}
                                    </span>
                                    <span class="d-block">
                                        <i class="bx bx-phone me-1"></i>
                                        {{ $contact->phone ?? 'Non spécifié' }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <p>{{ $contact->message }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <h5 class="mb-3">Mettre à jour le statut et l'affectation</h5>
                            <form action="{{ route('admin.contacts.update', $contact) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                            @foreach($statusOptions as $status)
                                                <option value="{{ $status }}" {{ old('status', $contact->status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="priority" class="form-label">Priorité</label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                            @foreach($priorityOptions as $priority)
                                                <option value="{{ $priority }}" {{ old('priority', $contact->priority) == $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                                            @endforeach
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="assigned_to" class="form-label">Assigné à</label>
                                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                            <option value="">Non assigné</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $contact->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-save me-1"></i>Mettre à jour le message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Envoyer une réponse</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.contacts.sendResponse', $contact) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="response" class="form-label">Votre réponse <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('response') is-invalid @enderror" id="response" name="response" rows="5" placeholder="Écrivez votre réponse ici...">{{ old('response') }}</textarea>
                                @error('response')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-mail-send me-1"></i>Envoyer la réponse
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(!$contact->read)
                                <a href="{{ route('admin.contacts.markAsRead', $contact) }}" class="btn btn-outline-success btn-sm">
                                    <i class="bx bx-check-double me-1"></i>Marquer comme lu
                                </a>
                            @else
                                <a href="{{ route('admin.contacts.markAsUnread', $contact) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bx bx-envelope me-1"></i>Marquer comme non lu
                                </a>
                            @endif
                            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                    <i class="bx bx-trash me-1"></i>Supprimer le message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Informations</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Statut:</strong>
                                @php
                                    $statusClass = [
                                        'new' => 'primary',
                                        'in_progress' => 'info',
                                        'resolved' => 'success',
                                        'closed' => 'dark',
                                    ][$contact->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($contact->status) }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Priorité:</strong>
                                @php
                                    $priorityClass = [
                                        'low' => 'success',
                                        'medium' => 'info',
                                        'high' => 'warning',
                                        'urgent' => 'danger',
                                    ][$contact->priority] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $priorityClass }}">{{ ucfirst($contact->priority) }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Lu:</strong>
                                <span class="badge bg-{{ $contact->read ? 'success' : 'danger' }}">{{ $contact->read ? 'Oui' : 'Non' }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Assigné à:</strong>
                                {{ $contact->assignedTo->name ?? 'Non assigné' }}
                            </li>
                            <li class="mb-2">
                                <strong>Créé le:</strong> {{ $contact->created_at?->format('d/m/Y H:i') }}
                            </li>
                            <li>
                                <strong>Modifié le:</strong> {{ $contact->updated_at?->format('d/m/Y H:i') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
