@extends('admin.common.layout')

@section('title', 'Create Crop Type')

@section('custom_header')
@endsection

@section('main')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Crop Type</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appmaster.createcroptype') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="crop_type">Crop Type</label>
                    <input type="text" class="form-control @error('crop_type') is-invalid @enderror" id="crop_type"
                        name="crop_type" placeholder="Crop Type" value="{{ old('crop_type') }}">
                    @error('crop_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="crop_type_assamese">Crop Type (Assamese)</label>
                    <input type="text" class="form-control @error('crop_type_assamese') is-invalid @enderror"
                        id="crop_type_assamese" name="crop_type_assamese" placeholder="Crop Type in Assamese"
                        value="{{ old('crop_type_assamese') }}">
                    @error('crop_type_assamese')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.appmaster.croptype') }}" class="btn btn-warning">Cancel</a>
            </form>
        </div>
    </div>
@endsection
@section('custom_js')
@endsection
