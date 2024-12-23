@extends('admin.common.layout')

@section('title', 'Recommendation Management')

@section('custom_header')
@endsection

@section('main')

    @if ($message = Session::get('success'))
        <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card text-dark-black">
        <div class="d-flex align-items-center">
            <h5 class="card-header">Recommendation Management</h5>
            <div>
                <a href="{{ route('admin.appmaster.createrecommendation') }}" class="btn btn-outline-success">
                    <i class="tf-icons bx bx-plus-medical"></i>Add New Recommendation
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap text-dark-black">
            <table class="table table-responsive text-nowrap" id="tblCropDesease">
                <thead>
                    <tr >
                        <th>Sl. No.</th>
                        <th>Control Measure</th>
                        <th>Control Measure Assamese</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-dark-black">
                    @forelse ($data as $index => $item)
                        <tr>
                            <td >{{ $index + 1 }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->control_measure }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->control_measure_as }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-mapping-id="{{ $item->mapping_id }}"
                                    data-disease-cd="{{ $item->disease_cd }}" data-crop-name-cd="{{ $item->crop_name_cd }}"
                                    data-control-measure="{{ $item->control_measure }}"
                                    data-control-measure-as="{{ $item->control_measure_as }}">
                                    <i class="tf-icons bx bx-edit"></i> Edit
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-mapping_id="{{ $item->mapping_id }}">
                                    <i class="tf-icons bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="{{ route('admin.appmaster.recommendation.update') }}"
                    autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="control_measure" class="form-label">Control Measure</label>
                            <textarea rows="4" class="form-control" id="control_measure" name="control_measure"
                                placeholder="Enter Control Measure"></textarea>
                            <div class="invalid-feedback control_measure-feedback" style="display: none;">
                                Please provide Control Measure.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="control_measure_as" class="form-label">Control Measure Assamese</label>
                            <textarea rows="4" class="form-control" id="control_measure_as" name="control_measure_as"
                                placeholder="Enter Control Measure (Assamese)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="disease_cd" class="form-label">Select Disease</label>
                            <select class="form-select @error('disease_cd') is-invalid @enderror" id="disease_cd"
                                name="disease_cd">
                                <option value="">Select Disease</option>
                                @foreach ($disease as $id => $desc)
                                    <option value="{{ $id }}">{{ $desc }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback disease-feedback" style="display: none;">
                                Please Select a Disease.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="crop_name_cd" class="form-label">Select Crop Name</label>
                            <select class="form-select @error('crop_name_cd') is-invalid @enderror" id="crop_name_cd"
                                name="crop_name_cd">
                                <option value="">Select Crop Name</option>
                                @foreach ($crops as $code => $description)
                                    <option value="{{ $code }}">{{ $description }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback crop_name_cd-feedback" style="display: none;">
                                Please select a Crop Name.
                            </div>
                        </div>
                        <input type="hidden" name="mapping_id" id="mapping_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="{{ route('admin.appmaster.recommendation.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this recommendation?</p>
                        <input type="hidden" name="mapping_id" id="deleteId">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successAlert = document.getElementById('successAlert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.opacity = '0';
                    successAlert.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(function() {
                        successAlert.remove();
                    }, 500);
                }, 5000);
            }
        });

        $(document).ready(function() {
            $('#tblCropDesease').DataTable();

            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var mappingId = button.data('mapping-id');
                var controlMeasure = button.data('control-measure');
                var controlMeasureAs = button.data('control-measure-as');
                var diseaseCd = button.data('disease-cd');
                var cropNameCd = button.data('crop-name-cd');

                var modal = $(this);
                modal.find('#mapping_id').val(mappingId);
                modal.find('#control_measure').val(controlMeasure);
                modal.find('#control_measure_as').val(controlMeasureAs);
                modal.find('#disease_cd').val(diseaseCd);
                modal.find('#crop_name_cd').val(cropNameCd).trigger('change');
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                var isValid = true;
                var controlMeasure = $('#control_measure').val().trim();
                var controlMeasureAs = $('#control_measure_as').val().trim();
                var diseaseCd = $('#disease_cd').val().trim();
                var cropNameCd = $('#crop_name_cd').val().trim();
                var form = $(this);

                $('#control_measure').removeClass('is-invalid');
                $('#disease_cd').removeClass('is-invalid');
                $('#crop_name_cd').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                if (controlMeasure === '') {
                    $('#control_measure').addClass('is-invalid');
                    $('.invalid-feedback.control_measure-feedback').show();
                    isValid = false;
                }

                if (diseaseCd === '') {
                    $('#disease_cd').addClass('is-invalid');
                    $('.invalid-feedback.disease-feedback').show();
                    isValid = false;
                }

                if (cropNameCd === '') {
                    $('#crop_name_cd').addClass('is-invalid');
                    $('.invalid-feedback.crop_name_cd-feedback').show();
                    isValid = false;
                }

                if (isValid) {
                    form.off('submit').submit();
                }
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var mapping_id = button.data('mapping_id');

                var modal = $(this);
                modal.find('#deleteId').val(mapping_id);
            });

            // Handle form submission for deletion
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        form.off('submit').submit();
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr);
                    }
                });
            });
        });
    </script>
@endsection
