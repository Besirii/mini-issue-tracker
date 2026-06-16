{{-- Expects: $action (string url), $method ('POST'|'PUT'), $project (Project|null), $submitLabel --}}
<form method="POST" action="{{ $action }}">
    @csrf
    @if (($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div class="mb-3">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input id="name" name="name" type="text"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $project?->name) }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="description">Description</label>
        <textarea id="description" name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $project?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="start_date">Start date</label>
            <input id="start_date" name="start_date" type="date"
                   class="form-control @error('start_date') is-invalid @enderror"
                   value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}">
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="deadline">Deadline</label>
            <input id="deadline" name="deadline" type="date"
                   class="form-control @error('deadline') is-invalid @enderror"
                   value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}">
            @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ $submitLabel ?? 'Save' }}</button>
        <a class="btn btn-outline-secondary" href="{{ url()->previous() }}">Cancel</a>
    </div>
</form>
