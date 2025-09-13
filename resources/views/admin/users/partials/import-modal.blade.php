<!-- Modal pour l'importation d'utilisateurs -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="importForm" action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bx bx-import text-primary me-2"></i>
                        Importer des Utilisateurs
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Instructions -->
                    <div class="alert alert-info" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Instructions :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Le fichier doit être au format CSV ou TXT</li>
                            <li>Colonnes requises : nom, email</li>
                            <li>Colonnes optionnelles : password, roles</li>
                            <li>La première ligne doit contenir les en-têtes</li>
                        </ul>
                    </div>

                    <!-- Sélection du fichier -->
                    <div class="mb-4">
                        <label for="csvFile" class="form-label">
                            <i class="bx bx-file me-1"></i>
                            Fichier CSV/TXT
                        </label>
                        <input class="form-control" type="file" id="csvFile" name="csv_file" accept=".csv,.txt" required>
                        <div class="form-text">
                            Taille maximale : 2MB. Formats acceptés : .csv, .txt
                        </div>
                    </div>

                    <!-- Options d'importation -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="csvDelimiter" class="form-label">Séparateur</label>
                                <select class="form-select" id="csvDelimiter" name="delimiter">
                                    <option value="," selected>Virgule (,)</option>
                                    <option value=";">Point-virgule (;)</option>
                                    <option value="|">Pipe (|)</option>
                                    <option value="	">Tabulation</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="csvEncoding" class="form-label">Encodage</label>
                                <select class="form-select" id="csvEncoding" name="encoding">
                                    <option value="utf-8" selected>UTF-8</option>
                                    <option value="iso-8859-1">ISO-8859-1</option>
                                    <option value="windows-1252">Windows-1252</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Options avancées -->
                    <div class="border rounded p-3 mb-3">
                        <h6 class="mb-3">
                            <i class="bx bx-cog me-1"></i>
                            Options avancées
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="skipHeader" name="skip_header" value="1" checked>
                                    <label class="form-check-label" for="skipHeader">
                                        Ignorer la première ligne (en-têtes)
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sendWelcomeEmail" name="send_welcome_email" value="1">
                                    <label class="form-check-label" for="sendWelcomeEmail">
                                        Envoyer un email de bienvenue
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="generatePassword" name="generate_password" value="1" checked>
                                    <label class="form-check-label" for="generatePassword">
                                        Générer automatiquement les mots de passe manquants
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing" value="1">
                                    <label class="form-check-label" for="updateExisting">
                                        Mettre à jour les utilisateurs existants (basé sur l'email)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rôle par défaut -->
                    <div class="mb-3">
                        <label for="defaultRole" class="form-label">
                            <i class="bx bx-shield me-1"></i>
                            Rôle par défaut (optionnel)
                        </label>
                        <select class="form-select" id="defaultRole" name="default_role">
                            <option value="">Aucun rôle par défaut</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Ce rôle sera assigné à tous les utilisateurs importés qui n'ont pas de rôle spécifié
                        </div>
                    </div>

                    <!-- Prévisualisation -->
                    <div id="previewSection" class="d-none">
                        <h6 class="mb-2">
                            <i class="bx bx-show me-1"></i>
                            Prévisualisation
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead id="previewHeader" class="table-light">
                                    <!-- Les en-têtes seront générés dynamiquement -->
                                </thead>
                                <tbody id="previewBody">
                                    <!-- Les données seront générées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">Aperçu des 5 premières lignes</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        Annuler
                    </button>
                    <button type="button" class="btn btn-info" id="previewBtn" disabled>
                        <i class="bx bx-show me-1"></i>
                        Prévisualiser
                    </button>
                    <button type="submit" class="btn btn-primary" id="importBtn" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <i class="bx bx-import me-1"></i>
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csvFileInput = document.getElementById('csvFile');
    const previewBtn = document.getElementById('previewBtn');
    const importBtn = document.getElementById('importBtn');
    const previewSection = document.getElementById('previewSection');

    // Activer les boutons quand un fichier est sélectionné
    csvFileInput?.addEventListener('change', function() {
        const hasFile = this.files.length > 0;
        previewBtn.disabled = !hasFile;
        importBtn.disabled = !hasFile;

        if (!hasFile) {
            previewSection.classList.add('d-none');
        }
    });

    // Prévisualisation
    previewBtn?.addEventListener('click', function() {
        const file = csvFileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const csv = e.target.result;
            const delimiter = document.getElementById('csvDelimiter').value;
            const lines = csv.split('\n').filter(line => line.trim());

            if (lines.length === 0) {
                alert('Le fichier semble être vide.');
                return;
            }

            const previewHeader = document.getElementById('previewHeader');
            const previewBody = document.getElementById('previewBody');

            // En-têtes
            const headers = lines[0].split(delimiter);
            previewHeader.innerHTML = '<tr>' + headers.map(h => `<th>${h.trim()}</th>`).join('') + '</tr>';

            // Données (5 premières lignes max)
            const dataLines = lines.slice(1, 6);
            previewBody.innerHTML = dataLines.map(line => {
                const cells = line.split(delimiter);
                return '<tr>' + cells.map(cell => `<td>${cell.trim()}</td>`).join('') + '</tr>';
            }).join('');

            previewSection.classList.remove('d-none');
        };

        reader.readAsText(file);
    });

    // Soumission du formulaire
    document.getElementById('importForm')?.addEventListener('submit', function(e) {
        const spinner = importBtn.querySelector('.spinner-border');
        const icon = importBtn.querySelector('.bx-import');

        // Afficher le spinner
        spinner?.classList.remove('d-none');
        icon?.classList.add('d-none');

        importBtn.disabled = true;
        importBtn.innerHTML = importBtn.innerHTML.replace('Importer', 'Importation en cours...');
    });
});
</script>
