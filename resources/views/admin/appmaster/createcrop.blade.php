@extends('admin.common.layout')

@section('title', 'Create Crop')

@section('custom_header')
@endsection

@section('main')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Add Crop</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.appmaster.createcrop') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="crop_name">Crop Name</label>
                <input type="text" class="form-control @error('crop_name') is-invalid @enderror" id="crop_name" name="crop_name" placeholder="Crop Name" value="{{ old('crop_name') }}">
                @error('crop_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="crop_name_assamese">Crop Name (Assamese)</label>
                <input type="text" class="form-control @error('crop_name_assamese') is-invalid @enderror" id="crop_name_assamese" name="crop_name_assamese" placeholder="Crop Name in Assamese" value="{{ old('crop_name_assamese') }}">
                @error('crop_name_assamese')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="crop_registry_no">Crop Registry Number</label>
                <input type="text" class="form-control @error('crop_registry_no') is-invalid @enderror" id="crop_registry_no" name="crop_registry_no" placeholder="Crop Registry Number" value="{{ old('crop_registry_no') }}">
                @error('crop_registry_no')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="scientific_name">Scientific Name</label>
                <input type="text" class="form-control @error('scientific_name') is-invalid @enderror" id="scientific_name" name="scientific_name" placeholder="Scientific Name" value="{{ old('scientific_name') }}">
                @error('scientific_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="crop_type_cd">Crop Type</label>
                <select class="form-select @error('crop_type_cd') is-invalid @enderror" id="crop_type_cd" name="crop_type_cd">
                    <option value="">Select Crop Type</option>
                    @foreach ($cropTypes as $id => $desc)
                        <option value="{{ $id }}" {{ old('crop_type_cd') == $id ? 'selected' : '' }}>
                            {{ $desc }}
                        </option>
                    @endforeach
                </select>
                @error('crop_type_cd')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.appmaster.cropinformation') }}" class="btn btn-warning">Cancel</a>
        </form>
    </div>
  </div>
@endsection
@section('custom_js')
@endsection
