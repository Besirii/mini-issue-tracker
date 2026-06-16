@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <h1 class="h3 mb-3">Tags</h1>

    <div class="row g-4">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Create a tag</h2>

                    @include('partials.validation-errors')

                    <form method="POST" action="{{ route('tags.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="color">Color</label>
                            <div class="input-group">
                                <input type="color" id="color-picker" class="form-control form-control-color"
                                       value="{{ old('color', '#2563eb') }}" title="Pick a color">
                                <input id="color" name="color" type="text" value="{{ old('color') }}"
                                       class="form-control @error('color') is-invalid @enderror" placeholder="#2563eb">
                                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-text">Optional hex code, e.g. #2563eb.</div>
                        </div>
                        <button class="btn btn-primary w-100">Create tag</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 col-lg-8">
            @if ($tags->isEmpty())
                <div class="empty-state">No tags yet. Create your first one.</div>
            @else
                <div class="card shadow-sm">
                    <ul class="list-group list-group-flush">
                        @foreach ($tags as $tag)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @include('issues.partials.tag-chip', ['tag' => $tag])
                                <span class="text-muted small">{{ $tag->issues_count }} issues</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Keep the hex text field in sync with the native color picker.
            const picker = document.getElementById('color-picker');
            const colorField = document.getElementById('color');
            if (picker && colorField) {
                picker.addEventListener('input', () => { colorField.value = picker.value; });
            }
        </script>
    @endpush
@endsection
