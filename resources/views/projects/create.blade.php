@extends('layouts.app')

@section('title', 'New project')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-3">New project</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('projects.partials.form', [
                        'action' => route('projects.store'),
                        'method' => 'POST',
                        'project' => null,
                        'submitLabel' => 'Create project',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
