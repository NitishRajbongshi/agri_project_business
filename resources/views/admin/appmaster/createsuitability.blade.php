@extends('admin.common.layout')

@section('title', 'Create Suitability')

@section('custom_header')
@endsection

@section('main')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Suitability</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appmaster.createsuitability') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="crop_type_cd">Crop Type</label>
                    <select class="form-select @error('crop_type_cd') is-invalid @enderror" id="crop_type_cd"
                        name="crop_type_cd">
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

                <div class="mb-3">
                    <label class="form-label" for="crop_name_cd">Crop Name</label>
                    <select class="form-select @error('crop_name_cd') is-invalid @enderror" id="crop_name_cd"
                        name="crop_name_cd">
                        <option value="">Select Crop Name</option>
                        <!-- Crop names will be populated here via JavaScript -->
                    </select>
                    @error('crop_name_cd')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="soil">Soil</label>
                    <textarea rows="4" class="form-control @error('soil') is-invalid @enderror" id="soil" name="soil"
                        placeholder="Soil" value="{{ old('soil') }}"></textarea>
                    @error('soil')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="soil_as">Soil (Assamese)</label>
                    <textarea rows="4" class="form-control @error('soil_as') is-invalid @enderror" id="soil_as" name="soil_as"
                        placeholder="Soil in Assamese" value="{{ old('soil_as') }}"></textarea>
                    @error('soil_as')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="sowing_time">Sowing Time</label>
                    <textarea rows="4" class="form-control @error('sowing_time') is-invalid @enderror" id="sowing_time"
                        name="sowing_time" placeholder="Sowing Time" value="{{ old('sowing_time') }}"></textarea>
                    @error('sowing_time')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="sowing_time_as">Sowing Time (Assamese)</label>
                    <textarea rows="4" class="form-control @error('sowing_time_as') is-invalid @enderror" id="sowing_time_as"
                        name="sowing_time_as" placeholder="Sowing Time in Assamese" value="{{ old('sowing_time_as') }}"></textarea>
                    @error('sowing_time_as')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.appmaster.suitability') }}" class="btn btn-warning">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@section('custom_js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#crop_type_cd').change(function() {
                var cropTypeCd = $(this).val();
                if (cropTypeCd) {
                    $.ajax({
                        url: '{{ route('admin.appmaster.getCropNames') }}',
                        type: 'GET',
                        data: {
                            crop_type_cd: cropTypeCd
                        },
                        success: function(data) {
                            var cropNameSelect = $('#crop_name_cd');
                            cropNameSelect.empty(); // Clear existing options
                            cropNameSelect.append('<option value="">Select Crop Name</option>');
                            var sortedData = Object.entries(data).sort(function(a, b) {
                                return a[1].localeCompare(b[
                                    1]); // Compare crop names alphabetically
                            });

                            // Append sorted crop names to the dropdown
                            $.each(sortedData, function(index, [key, value]) {
                                cropNameSelect.append('<option value="' + key + '">' +
                                    value.toUpperCase() + '</option>');
                            });
                        }
                    });
                } else {
                    $('#crop_name_cd').empty().append('<option value="">Select Crop Name</option>');
                }
            });
        });
    </script>
@endsection
