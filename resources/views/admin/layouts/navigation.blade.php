<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
    <div class="flex-1 flex flex-col min-h-0 bg-gray-800">
        <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
            <h1 class="text-white text-xl font-bold">Nova Tech Admin</h1>
        </div>
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.projects.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-project-diagram mr-3"></i>
                    Projets
                </a>

                <a href="{{ route('admin.services.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-tools mr-3"></i>
                    Services
                </a>

                <a href="{{ route('admin.products.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-box mr-3"></i>
                    Produits
                </a>

                <a href="{{ route('admin.testimonials.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-comments mr-3"></i>
                    Témoignages
                </a>

                <a href="{{ route('admin.team.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-users mr-3"></i>
                    Équipe
                </a>

                <a href="{{ route('admin.contacts.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class="fas fa-envelope mr-3"></i>
                    Messages
                    @php($unreadCount = \App\Models\Contact::where('read', false)->count())
                    @if($unreadCount > 0)
                        <span class="ml-auto inline-block py-0.5 px-3 text-xs font-medium rounded-full bg-red-600 text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Déconnexion
                    </button>
                </form>
            </nav>
        </div>
    </div>
</div>
