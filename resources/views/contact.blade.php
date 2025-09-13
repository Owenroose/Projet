@extends('layouts.app')

@section('title', 'Contactez-nous - Nova Tech Bénin')
@section('description', 'Contactez Nova Tech Bénin pour discuter de vos projets de développement web/mobile, d\'achat de matériel informatique ou de conseil technique.')

@section('content')

<!-- Section du Hero (Header) -->
<section class="bg-gray-900 text-center text-white py-24 md:py-32">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4">
                Contactez
                <strong class="block text-yellow-400">Nova Tech Bénin</strong>
            </h1>
            <p class="text-base md:text-lg mb-8 max-w-2xl mx-auto">
                Nous sommes là pour répondre à vos questions et vous accompagner dans vos projets
            </p>
        </div>
    </div>
</section>

<!-- Section de contact principale -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Colonne des informations de contact -->
            <div class="lg:col-span-1">
                <div class="bg-gray-100 rounded-lg p-8 shadow-inner h-full">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Nos coordonnées</h3>

                    <!-- Adresse -->
                    <div class="flex items-start mb-6">
                        <div class="text-blue-600 text-2xl mr-4 mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700">Adresse</h4>
                            <p class="text-gray-600">Cotonou, Littoral<br>République du Bénin</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start mb-6">
                        <div class="text-blue-600 text-2xl mr-4 mt-1">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700">Email</h4>
                            <a href="mailto:contact@novatechbenin.com" class="text-gray-600 hover:underline">contact@novatechbenin.com</a>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="flex items-start mb-6">
                        <div class="text-blue-600 text-2xl mr-4 mt-1">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700">Téléphone</h4>
                            <a href="tel:+22997000000" class="text-gray-600 hover:underline">+229 97 00 00 00</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne du formulaire de contact -->
            <div class="md:col-span-2">
                <div class="bg-gray-50 rounded-lg p-8 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Envoyez-nous un message</h3>
                    <form id="contactForm" action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <div class="md:col-span-1">
                            <label for="name" class="block text-gray-700 font-semibold mb-2">Votre nom *</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Votre nom complet" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="email" class="block text-gray-700 font-semibold mb-2">Votre email *</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="exemple@domaine.com" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="phone" class="block text-gray-700 font-semibold mb-2">Numéro de téléphone</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+229 97000000">
                        </div>
                        <div class="md:col-span-1">
                            <label for="subject" class="block text-gray-700 font-semibold mb-2">Sujet *</label>
                            <input type="text" id="subject" name="subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Sujet de votre message" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="message" class="block text-gray-700 font-semibold mb-2">Votre message *</label>
                            <textarea id="message" name="message" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Écrivez votre message ici..." required></textarea>
                        </div>
                        <div class="md:col-span-2 text-right">
                            <button type="submit" class="nova-btn nova-btn-primary">
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Google Maps -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Où nous trouver</h2>
        <div class="relative w-full h-96 rounded-lg overflow-hidden shadow-lg">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.577232233816!2d2.417218614778335!3d6.45260199534571!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b41e3d3b3c3b5%3A0xc3f1245b73d8a6b1!2sCotonou%2C%20B%C3%A9nin!5e0!3m2!1sfr!2sng!4v1620000000000!5m2!1sfr!2sng" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('contactForm');
        const inputs = form.querySelectorAll('input, textarea');

        // Fonction pour afficher un message d'erreur
        function showFieldError(field, message) {
            clearFieldError(field); // Effacer l'erreur précédente

            field.classList.add('border-red-500', 'focus:ring-red-500');

            const errorDiv = document.createElement('p');
            errorDiv.className = 'text-red-500 text-sm mt-1';
            errorDiv.textContent = message;

            field.parentNode.appendChild(errorDiv);
        }

        // Fonction pour effacer les messages d'erreur
        function clearFieldError(field) {
            field.classList.remove('border-red-500', 'focus:ring-red-500');

            const existingError = field.parentNode.querySelector('p.text-red-500');
            if (existingError) {
                existingError.remove();
            }
        }

        // Validation du champ
        function validateField(field) {
            const fieldName = field.name;
            const value = field.value.trim();

            if (field.hasAttribute('required') && !value) {
                showFieldError(field, 'Ce champ est requis.');
                return false;
            }

            if (fieldName === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showFieldError(field, 'Veuillez entrer une adresse email valide.');
                    return false;
                }
            }

            if (fieldName === 'phone' && value) {
                const phoneRegex = /^[+]?[0-9\s-()]{8,20}$/;
                if (!phoneRegex.test(value)) {
                    showFieldError(field, 'Veuillez entrer un numéro de téléphone valide.');
                    return false;
                }
            }

            clearFieldError(field);
            return true;
        }

        // Validation en temps réel et au blur
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('border-red-500')) {
                    validateField(input);
                }
            });
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();

                const firstError = form.querySelector('.border-red-500');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
</script>
@endpush
