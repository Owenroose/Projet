@extends('admin.layouts.app')

@section('title', 'Modifier le Projet')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration / Projets /</span> Modifier "{{ $project->title }}"
</h4>

<form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.projects._form')
</form>
@endsection
