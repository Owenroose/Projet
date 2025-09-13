<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ isset($project) ? 'Modifier le projet' : 'Informations générales' }}</h5>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>Retour à la liste
        </a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="title" class="form-label">Titre du projet <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror"
                   id="title" name="title" value="{{ old('title', $project->title ?? '') }}"
                   placeholder="Ex: Site e-commerce React">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description" rows="4"
                      placeholder="Description détaillée du projet">{{ old('description', $project->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="challenge" class="form-label">Défi / Problématique</label>
            <textarea class="form-control @error('challenge') is-invalid @enderror"
                      id="challenge" name="challenge" rows="3"
                      placeholder="Quel était le défi à relever ?">{{ old('challenge', $project->challenge ?? '') }}</textarea>
            @error('challenge')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="solution" class="form-label">Solution apportée</label>
            <textarea class="form-control @error('solution') is-invalid @enderror"
                      id="solution" name="solution" rows="3"
                      placeholder="Comment avez-vous résolu ce problème ?">{{ old('solution', $project->solution ?? '') }}</textarea>
            @error('solution')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="technologies" class="form-label">Technologies utilisées</label>
            <input type="text" class="form-control @error('technologies') is-invalid @enderror"
                   id="technologies" name="technologies" value="{{ old('technologies', $project->technologies ?? '') }}"
                   placeholder="Ex: Laravel, Vue.js, Tailwind CSS">
            <small class="text-muted">Séparez les technologies par des virgules (,).</small>
            @error('technologies')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informations du client</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="client" class="form-label">Nom du client</label>
                    <input type="text" class="form-control @error('client') is-invalid @enderror"
                           id="client" name="client" value="{{ old('client', $project->client ?? '') }}"
                           placeholder="Ex: Entreprise X">
                    @error('client')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="project_date" class="form-label">Date du projet</label>
                    <input type="date" class="form-control @error('project_date') is-invalid @enderror"
                           id="project_date" name="project_date" value="{{ old('project_date', isset($project) ? $project->project_date?->format('Y-m-d') : '') }}">
                    @error('project_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="project_url" class="form-label">URL du projet</label>
                    <input type="url" class="form-control @error('project_url') is-invalid @enderror"
                           id="project_url" name="project_url" value="{{ old('project_url', $project->project_url ?? '') }}"
                           placeholder="Ex: https://www.novatechbenin.com">
                    @error('project_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Statut & Image</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="image" class="form-label">Image principale <span class="text-danger">*</span></label>
                    @if(isset($project) && $project->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $project->image) }}" alt="Image actuelle" class="img-fluid rounded" style="max-height: 200px;">
                            <small class="d-block text-muted mt-2">Image actuelle du projet. Pour la modifier, choisissez un nouveau fichier ci-dessous.</small>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="order" class="form-label">Ordre d'affichage</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror"
                           id="order" name="order" value="{{ old('order', $project->order ?? '') }}"
                           placeholder="Ex: 1">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                           {{ old('featured', $project->featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="featured">En vedette sur la page d'accueil</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="published" name="published" value="1"
                           {{ old('published', $project->published ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="published">Publier le projet</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bx bx-save me-1"></i>{{ isset($project) ? 'Mettre à jour' : 'Créer' }} le projet
    </button>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
        Annuler
    </a>
</div>
