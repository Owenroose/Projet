@extends('layouts.app')

@section('title', $service->name)
@section('description', $service->description)

@section('content')
    <div class="container mx-auto px-4 py-12 md:py-16">
        <div class="relative bg-gray-900 text-white rounded-xl p-6 md:p-10 mb-12 shadow-lg overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-700 opacity-90 z-0"></div>
            <div class="relative z-10 text-center">
                <div class="inline-block p-4 rounded-full bg-white text-blue-600 mb-6 shadow-md">
                    <i class="fas fa-{{ $service->icon }} text-5xl"></i>
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">{{ $service->name }}</h1>
            </div>
        </div>

        @if ($service->image)
            <div class="mb-12">
                {{-- Le chemin corrigé pour la nouvelle structure d'images --}}
                <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="rounded-xl shadow-2xl w-full h-80 md:h-96 object-cover">
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mb-12">
            @if ($service->features)
                <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-6 md:p-8 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-blue-400 pb-2">Fonctionnalités clés</h2>
                    <ul class="space-y-4 text-gray-700">
                        @foreach (json_decode($service->features, true) as $feature)
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-blue-400 pb-2">Notre service en détail</h2>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! $service->full_description ?? $service->description !!}
                </div>
            </div>
        </div>

        <div class="text-center py-12 bg-blue-50 rounded-lg shadow-inner mb-16">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">Prêt à démarrer votre projet ?</h2>
            <p class="text-gray-700 mb-6 max-w-xl mx-auto">Contactez notre équipe pour discuter de vos besoins spécifiques et obtenir un devis sur mesure, sans engagement.</p>
            <a href="{{ url('/contact') }}" class="nova-btn nova-btn-primary">Demander un devis</a>
        </div>

        @if (isset($relatedProjects) && $relatedProjects->count() > 0)
            <div class="mt-16">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">Projets réalisés dans ce domaine</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($relatedProjects as $project)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                            @if ($project->image)
                                {{-- Le chemin corrigé pour les images de projet --}}
                                <img src="{{ asset($project->image) }}" alt="{{ $project->title }}" class="w-full h-48 object-cover">
                            @endif
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $project->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $project->description }}</p>
                                <a href="{{ url('/projects/' . $project->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">Découvrir le projet &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
