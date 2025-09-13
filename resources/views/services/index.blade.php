@extends('layouts.app')

@section('title', 'Nos Services - Nova Tech Bénin')
@section('description', 'Découvrez la gamme complète de services de Nova Tech Bénin, incluant le développement web, mobile et la vente de matériel informatique.')

@section('content')
<section class="bg-blue-600 text-white py-20 md:py-32">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Nos Services</h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto">
            Des solutions sur mesure pour propulser votre entreprise vers la réussite numérique.
        </p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <a href="{{ route('services.show', $service->slug) }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                <div class="flex flex-col items-center text-center">
                    @if($service->icon)
                    <div class="mb-4">
                        <i class="{{ $service->icon }} text-5xl text-blue-600"></i>
                    </div>
                    @endif
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $service->name }}</h2>
                    <p class="text-gray-600 text-sm">
                        {{ \Illuminate\Support\Str::limit($service->description, 150) }}
                    </p>
                </div>
            </a>
            @empty
            <div class="md:col-span-2 lg:col-span-3 text-center py-10">
                <p class="text-gray-600">Aucun service n'est disponible pour le moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
