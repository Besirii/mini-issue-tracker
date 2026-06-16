@extends('layouts.app')

@section('title', 'Edit issue')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-3">Edit issue</h1>

            @include('partials.validation-errors')

            <div class="card shadow-sm">
                <div class="card-body">
                    @include('issues.partials.form', [
                        'action' => route('issues.update', $issue),
                        'method' => 'PUT',
                        'issue' => $issue,
                        'submitLabel' => 'Save changes',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
