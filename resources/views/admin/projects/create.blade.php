@extends('admin.layouts.app')

@section('title', 'Créer un Projet')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration / Projets /</span> Nouveau Projet
</h4>

<form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.projects._form')
</form>
@endsection
