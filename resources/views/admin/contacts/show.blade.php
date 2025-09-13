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
                                <h6 class="mb-3">Informations du contact :</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Nom :</strong> {{ $contact->name }}</li>
                                    <li class="mb-2"><strong>Email :</strong> <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></li>
                                    <li class="mb-2"><strong>Téléphone :</strong> {{ $contact->phone ?? 'Non renseigné' }}</li>
                                    <li class="mb-2"><strong>Entreprise :</strong> {{ $contact->company ?? 'Non renseigné' }}</li>
                                    <li class="mb-2"><strong>Sujet :</strong> {{ $contact->subject }}</li>
                                </ul>

                                <hr class="my-4">

                                <h6>Message :</h6>
                                <p class="mb-0">{{ $contact->message }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Envoyer une Réponse</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.contacts.send-response', $contact) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="response" class="form-label">Votre réponse</label>
                                <textarea class="form-control" id="response" name="response" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-send me-1"></i>Envoyer la réponse
                            </button>
                        </form>
                        @if($contact->hasResponse())
                            <div class="mt-4">
                                <h6>Réponse envoyée le {{ $contact->response_sent_at->format('d/m/Y H:i') }} :</h6>
                                <div class="alert alert-secondary mt-2">{{ $contact->response }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title">Statut et Priorité</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.contacts.update', $contact) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}" {{ $contact->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priorité</label>
                                <select class="form-select" id="priority" name="priority">
                                    @foreach($priorityOptions as $key => $label)
                                        <option value="{{ $key }}" {{ $contact->priority == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigné à</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="">Non assigné</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $contact->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-save me-1"></i> Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title">Informations techniques</h5></div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Lu :</strong> <span class="badge bg-{{ $contact->read ? 'success' : 'danger' }}">{{ $contact->read ? 'Oui' : 'Non' }}</span></li>
                            <li class="mb-2"><strong>ID :</strong> {{ $contact->id }}</li>
                            <li class="mb-2"><strong>Créé le :</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</li>
                            <li class="mb-2"><strong>Modifié le :</strong> {{ $contact->updated_at->format('d/m/Y H:i') }}</li>
                            @if($contact->response_sent_at)
                                <li class="mb-2"><strong>Réponse envoyée le :</strong> {{ $contact->response_sent_at->format('d/m/Y H:i') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
