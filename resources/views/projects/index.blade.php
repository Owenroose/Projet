@extends('layouts.app')

@section('title', 'Nos Projets - Nova Tech Bénin')
@section('description', 'Découvrez les projets de développement web et mobile réalisés par Nova Tech Bénin pour ses clients.')

@section('content')
<section class="bg-blue-600 text-white py-20 md:py-32">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Nos Projets</h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto">
            Des idées concrétisées en succès numériques pour nos clients.
        </p>
    </div>
</section>

<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($projects as $project)
            <a href="{{ route('projects.show', $project->slug) }}" class="block bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $project->title }}</h2>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($project->description, 100) }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->technologies_array as $tech)
                        <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ trim($tech) }}</span>
                        @endforeach
                    </div>
                </div>
            </a>
            @empty
            <div class="md:col-span-2 lg:col-span-3 text-center py-10">
                <p class="text-gray-600">Aucun projet n'est disponible pour le moment.</p>
            </div>
            @endforelse
        </div>
        @if (method_exists($projects, 'links'))
        <div class="mt-8">
            {{ $projects->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</section>
@endsection
