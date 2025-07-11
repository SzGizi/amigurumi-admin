@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('Edit Amigurumi Pattern') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('amigurumi-patterns.update', $amigurumiPattern) }}">
        @csrf
        @method('PUT')

        <!-- Pattern Info -->
        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $amigurumiPattern->title) }}">
        </div>

        <div class="mb-3">
            <label for="yarn_description" class="form-label">{{ __('Yarn Description') }}</label>
            <textarea name="yarn_description" id="yarn_description" class="form-control">{{ old('yarn_description', $amigurumiPattern->yarn_description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tools_description" class="form-label">{{ __('Tools Description') }}</label>
            <textarea name="tools_description" id="tools_description" class="form-control">{{ old('tools_description', $amigurumiPattern->tools_description) }}</textarea>
        </div>

        <!-- Sections and Rows -->
        <h4 class="mt-4">{{ __('Sections') }}</h4>
        <div id="sections">
            @foreach ($amigurumiPattern->amigurumiSections as $sectionIndex => $section)
                <div class="card mb-3 p-3 section-block">
                    <button type="button" class="btn btn-danger btn-sm float-end remove-section">&times;</button>
                    <div class="mb-2">
                        <label class="form-label">{{ __('Section Title') }}</label>
                        <input type="text" name="sections[{{ $sectionIndex }}][title]" class="form-control" value="{{ $section->title }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">{{ __('Order') }}</label>
                        <input type="number" name="sections[{{ $sectionIndex }}][order]" class="form-control" value="{{ $section->order }}">
                    </div>

                    <div class="row-list">
                        <label class="form-label">{{ __('Rows') }}</label>
                        @foreach ($section->amigurumiRows as $rowIndex => $row)
                            <div class="border p-2 mb-2 row-block">
                                <button type="button" class="btn btn-sm btn-outline-danger float-end remove-row">&times;</button>
                                <input type="number" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][row_number]" class="form-control mb-1" placeholder="{{ __('Row number') }}" value="{{ $row->row_number }}">
                                <input type="text" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][instructions]" class="form-control" placeholder="{{ __('Instructions') }}" value="{{ $row->instructions }}">
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary add-row mt-2">{{ __('Add Row') }}</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary my-3" id="add-section">{{ __('Add Section') }}</button>

        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let sectionIndex = {{ $amigurumiPattern->amigurumiSections->count() }};

        document.getElementById('add-section').addEventListener('click', function () {
            const container = document.createElement('div');
            container.classList.add('card', 'mb-3', 'p-3', 'section-block');
            container.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm float-end remove-section">&times;</button>
                <div class="mb-2">
                    <label class="form-label">{{ __('Section Title') }}</label>
                    <input type="text" name="sections[${sectionIndex}][title]" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">{{ __('Order') }}</label>
                    <input type="number" name="sections[${sectionIndex}][order]" class="form-control">
                </div>
                <div class="row-list"></div>
                <button type="button" class="btn btn-secondary add-row mt-2">{{ __('Add Row') }}</button>
            `;
            document.getElementById('sections').appendChild(container);
            sectionIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-row')) {
                const section = e.target.closest('.section-block');
                const rows = section.querySelectorAll('.row-block').length;
                const sectionIdx = Array.from(document.querySelectorAll('.section-block')).indexOf(section);

                const rowDiv = document.createElement('div');
                rowDiv.classList.add('border', 'p-2', 'mb-2', 'row-block');
                rowDiv.innerHTML = `
                    <button type="button" class="btn btn-sm btn-outline-danger float-end remove-row">&times;</button>
                    <input type="number" name="sections[${sectionIdx}][rows][${rows}][row_number]" class="form-control mb-1" placeholder="{{ __('Row number') }}">
                    <input type="text" name="sections[${sectionIdx}][rows][${rows}][instructions]" class="form-control" placeholder="{{ __('Instructions') }}">
                `;
                section.querySelector('.row-list').appendChild(rowDiv);
            }

            if (e.target.classList.contains('remove-section')) {
                e.target.closest('.section-block').remove();
            }

            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.row-block').remove();
            }
        });
    });
</script>
@endsection
