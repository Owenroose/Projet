<footer class="bg-gray-800 text-gray-300 py-10 md:py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-block">
                    <span class="text-3xl font-extrabold text-blue-500">Nova Tech</span>
                </a>
                <p>Votre partenaire technologique au Bénin</p>
                <p class="text-sm text-gray-400">Solutions digitales innovantes et matériel informatique de qualité.</p>
            </div>

            <div>
                <h5 class="text-xl font-semibold text-white mb-4">Nos Services</h5>
                <ul class="space-y-2">
                    @php
                        // Récupère les services publiés et les trie par ordre
                        $services = App\Models\Service::published()->orderBy('order')->get();
                    @endphp
                    @foreach ($services as $service)
                        <li>
                            <a href="{{ route('services.show', $service->slug) }}" class="hover:text-white transition-colors duration-200">
                                {{ $service->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h5 class="text-xl font-semibold text-white mb-4">Contact</h5>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fa fa-map-marker-alt text-blue-500 mr-3 mt-1"></i>
                        <span>123 Avenue de la Technologie, Cotonou, Bénin</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fa fa-phone-alt text-blue-500 mr-3 mt-1"></i>
                        <span>+229 XX XX XX XX</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fa fa-envelope text-blue-500 mr-3 mt-1"></i>
                        <span>contact@novatechbenin.com</span>
                    </li>
                </ul>
                <div class="flex space-x-4 mt-6">
                    <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors duration-200">
                        <i class="fab fa-facebook-f text-2xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors duration-200">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors duration-200">
                        <i class="fab fa-linkedin-in text-2xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors duration-200">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <h5 class="text-xl font-semibold text-white mb-4">Restez à jour</h5>
                <p class="text-sm text-gray-400">Abonnez-vous à notre newsletter pour les dernières nouvelles et offres.</p>
                <form action="#" method="POST" class="flex flex-col sm:flex-row gap-2">
                    <input type="email" placeholder="Votre email" class="flex-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">S'abonner</button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-sm text-gray-500">
            <p>&copy; <span id="current-year"></span> Nova Tech Bénin. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script>
    document.getElementById('current-year').textContent = new Date().getFullYear();
</script>
