@extends('layouts.app')

@section('title', 'New issue')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-3">New issue</h1>

            @include('partials.validation-errors')

            <div class="card shadow-sm">
                <div class="card-body">
                    @include('issues.partials.form', [
                        'action' => route('issues.store'),
                        'method' => 'POST',
                        'issue' => null,
                        'submitLabel' => 'Create issue',
                        'selectedTagIds' => [],
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
