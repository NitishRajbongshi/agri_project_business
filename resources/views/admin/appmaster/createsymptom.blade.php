@extends('admin.common.layout')

@section('title', 'Create Disease')

@section('custom_header')
@endsection

@section('main')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Symptom</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appmaster.createsymptom') }}" method="POST" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="language_cd">Language</label>
                    <select class="form-select @error('language_cd') is-invalid @enderror" id="language_cd"
                        name="language_cd">
                        <option value="">Select Language</option>
                        @foreach ($languages as $id => $desc)
                            <option value="{{ $id }}" {{ old('language_cd') == $id ? 'selected' : '' }}>
                                {{ $desc }}
                            </option>
                        @endforeach
                    </select>
                    @error('language_cd')
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
                    <label class="form-label" for="symptom">Symptom</label>
                    <textarea rows="4" class="form-control @error('symptom') is-invalid @enderror" id="symptom"
                        name="symptom" placeholder="Symptom" value="{{ old('symptom') }}"></textarea>
                    @error('symptom')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>




                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.appmaster.symptom') }}" class="btn btn-warning">Cancel</a>
            </form>
        </div>
    </div>
@endsection
@section('custom_js')
@endsection
