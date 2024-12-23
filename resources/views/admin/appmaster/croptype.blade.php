@extends('admin.common.layout')

@section('title', 'Crop Type Management')

@section('custom_header')
@endsection

@section('main')

    @if ($message = Session::get('success'))
        <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="d-flex align-items-center">
            <h5 class="card-header">Crop Types Management</h5>
            <div>
                <a href="{{ route('admin.appmaster.createcroptype') }}" class="btn btn-outline-success">
                    <i class="tf-icons bx bx-plus-medical"></i>Add Crop Type
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap px-4">
            <table class="table" id="tblCropType">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Crop Type</th>
                        <th>Crop Type Assamese</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->crop_type_desc }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->crop_type_desc_as }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $item->crop_type_cd }}"
                                    data-crop-type="{{ $item->crop_type_desc }}"
                                    data-crop-type-as="{{ $item->crop_type_desc_as }}">
                                    <i class="tf-icons bx bx-edit"></i> Edit
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-crop_type_cd="{{ $item->crop_type_cd }}">
                                    <i class="tf-icons bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No data found</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Crop Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="{{ route('admin.appmaster.croptype.update') }}"
                    autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="crop_type_cd" id="cropTypeCd">
                        <div class="mb-3">
                            <label for="cropType" class="form-label">Crop Type</label>
                            <input type="text" class="form-control" id="cropType" name="crop_type_desc"
                                placeholder="Enter Crop Type">
                            <div class="invalid-feedback crop-type-feedback" style="display: none;">
                                Please provide a Crop Type.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cropTypeAs" class="form-label">Crop Type Assamese</label>
                            <input type="text" class="form-control" id="cropTypeAs" name="crop_type_desc_as"
                                placeholder="Enter Crop Type (Assamese)">
                        </div>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Crop Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="{{ route('admin.appmaster.croptype.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this crop type?</p>
                        <input type="hidden" name="crop_type_cd" id="deleteId">
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
            $('#tblCropType').DataTable();

            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var cropTypeCd = button.data('id');
                var cropType = button.data('crop-type');
                var cropTypeAs = button.data('crop-type-as');

                var modal = $(this);
                modal.find('#cropTypeCd').val(cropTypeCd);
                modal.find('#cropType').val(cropType);
                modal.find('#cropTypeAs').val(cropTypeAs);
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                var isValid = true;
                var cropType = $('#cropType').val().trim();
                var cropTypeCd = $('#cropTypeCd').val().trim();
                var form = $(this);

                $('#cropType').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                if (cropType === '') {
                    $('#cropType').addClass('is-invalid');
                    $('.invalid-feedback').filter('.crop-type-feedback').show();
                    isValid = false;
                }


                $.ajax({
                    data: {
                        _token: '{{ csrf_token() }}',
                        crop_type_cd: cropTypeCd
                    },
                    success: function(response) {
                        if (isValid) {
                            form.off('submit').submit();
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr);
                    }
                });

            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var crop_type_cd = button.data('crop_type_cd');

                var modal = $(this);
                modal.find('#deleteId').val(crop_type_cd);
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
