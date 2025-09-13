@extends('layouts.app')

@section('title', $project->title . ' - Nova Tech Bénin')
@section('description', Str::limit($project->description, 150))

@section('content')
<section class="bg-blue-600 text-white py-20 md:py-32">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $project->title }}</h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto">{{ $project->client }} - {{ $project->project_date->format('Y') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="bg-white p-8 rounded-lg shadow-md mb-12">
            <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="w-full h-auto rounded-lg shadow-lg mb-8">

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3 space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Le Défi</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $project->challenge }}</p>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">La Solution</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $project->solution }}</p>
                    </div>
                </div>

                <aside class="w-full lg:w-1/3 space-y-8">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Détails du projet</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-center gap-3"><i class="fas fa-user-tie text-blue-600"></i><span class="font-semibold">Client:</span><span>{{ $project->client }}</span></li>
                            <li class="flex items-center gap-3"><i class="fas fa-calendar-alt text-blue-600"></i><span class="font-semibold">Date:</span><span>{{ $project->project_date->format('F Y') }}</span></li>
                            <li class="flex items-center gap-3"><i class="fas fa-link text-blue-600"></i><span class="font-semibold">URL:</span><a href="{{ $project->project_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $project->project_url }}</a></li>
                        </ul>
                        <div class="mt-6 border-t pt-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Technologies</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->technologies_array as $tech)
                                <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-medium">{{ trim($tech) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

@if(isset($relatedProjects) && $relatedProjects->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Projets similaires</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($relatedProjects as $relatedProject)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <a href="{{ route('projects.show', $relatedProject->slug) }}">
                        <img src="{{ asset('storage/' . $relatedProject->image) }}" alt="{{ $relatedProject->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4 text-center">
                        <h4 class="font-semibold text-gray-800 mb-1">{{ $relatedProject->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $relatedProject->client }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
