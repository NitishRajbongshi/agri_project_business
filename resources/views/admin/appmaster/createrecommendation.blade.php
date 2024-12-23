@extends('admin.common.layout')

@section('title', 'Create Recommendation')

@section('custom_header')
@endsection

@section('main')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Recommendation</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appmaster.createrecommendation') }}" method="POST" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="crop_name_cd">Crop Name</label>
                    <select class="form-select @error('crop_name_cd') is-invalid @enderror" id="crop_name_cd"
                        name="crop_name_cd">
                        <option value="">Select Crop</option>
                        @foreach ($crops as $id => $desc)
                            <option value="{{ $id }}" {{ old('crop_name_cd') == $id ? 'selected' : '' }}>
                                {{ $desc }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_name_cd')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="disease_cd">Disease Name</label>
                    <select class="form-select @error('disease_cd') is-invalid @enderror" id="disease_cd" name="disease_cd">
                        <option value="">Select Disease Name</option>
                        @foreach ($disease as $id => $desc)
                            <option value="{{ $id }}" {{ old('disease_cd') == $id ? 'selected' : '' }}>
                                {{ $desc }}
                            </option>
                        @endforeach
                    </select>
                    @error('disease_cd')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label class="form-label" for="control_measure">Control Measure</label>
                    <textarea rows="4" class="form-control @error('control_measure') is-invalid @enderror" id="control_measure"
                        name="control_measure" placeholder="Control Measure" value="{{ old('control_measure') }}"></textarea>
                    @error('control_measure')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="control_measure_as">Control Measure (Assamese)</label>
                    <textarea rows="4" class="form-control @error('control_measure_as') is-invalid @enderror" id="control_measure_as"
                        name="control_measure_as" placeholder="Control Measure in Assamese" value="{{ old('control_measure_as') }}"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.appmaster.recommendation') }}" class="btn btn-warning">Cancel</a>
            </form>
        </div>
    </div>
@endsection
@section('custom_js')
@endsection
