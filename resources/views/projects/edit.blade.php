@extends('layouts.app')

@section('title', 'Edit project')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-3">Edit project</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('projects.partials.form', [
                        'action' => route('projects.update', $project),
                        'method' => 'PUT',
                        'project' => $project,
                        'submitLabel' => 'Save changes',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
