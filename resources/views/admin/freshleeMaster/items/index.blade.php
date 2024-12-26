@extends('admin.common.layout')

@section('title', 'Crop Name Management')

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
            <h5 class="card-header">Freshlee Market Item Information Management</h5>
            <div>
                <a href="{{ route('admin.freshlee.master.item.create') }}" class="btn btn-outline-success">
                    <i class="tf-icons bx bx-plus-medical"></i>Add Item
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap px-4">
            <table class="table" id="tblUser">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Item Name</th>
                        <th>Perishability</th>
                        <th>Category</th>
                        <th>Item Type</th>
                        <th>Farm Life</th>
                        <th>Min. Order</th>
                        <th>Item Unit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($marketItems as $index => $item)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->item_name }}</>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->perishability_descr }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->item_category_desc }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;">{{ $item->product_name }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;text-align: center;">{{ $item->farm_life_in_days }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;text-align: center;">{{ $item->min_qty_to_order }}</td>
                            <td style="overflow-wrap: break-word; white-space: normal;text-align: center;">{{ $item->unit_min_order_qty }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $item->item_cd }}"
                                    data-item-name="{{ $item->item_name }}"
                                    data-perishability-cd="{{ $item->perishability_cd }}"
                                    data-item-category="{{ $item->item_category_cd }}" 
                                    data-product-type-cd="{{ $item->product_type_cd }}">
                                    <i class="tf-icons bx bx-edit"></i> Edit
                                </a>
                                {{-- <a href="#" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-item_cd="{{ $item->item_cd }}">
                                    <i class="tf-icons bx bx-trash"></i>
                                </a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No data found.</td>
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
                <form id="editForm" method="POST" action="{{ route('admin.appmaster.crops.update') }}"
                    autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="crop_name_cd" id="cropNameCd">
                        <div class="mb-3">
                            <label for="cropName" class="form-label">Crop Name</label>
                            <input type="text" class="form-control" id="cropName" name="crop_name_desc"
                                placeholder=" Enter Crop Name">
                            <div class="invalid-feedback crop-name-feedback" style="display: none;">
                                Please provide a Crop Name.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cropNameAs" class="form-label">Crop Name Assamese</label>
                            <input type="text" class="form-control" id="cropNameAs" name="crop_name_desc_as"
                                placeholder=" Enter Crop Name (Assamese)">
                        </div>
                        <div class="mb-3">
                            <label for="registryNo" class="form-label">Crop Registry No</label>
                            <input type="text" class="form-control" id="registryNo" name="crop_registry_no"
                                placeholder=" Enter Crop Registry No">
                            <div class="invalid-feedback registry-no-feedback" style="display: none;">
                                The Crop Registry No is already in use.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="scientificName" class="form-label">Scientific Name</label>
                            <input type="text" class="form-control" id="scientificName" name="scientific_name"
                                placeholder=" Enter Scientific Name">
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Crop Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="{{ route('admin.appmaster.crops.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this crop information?</p>
                        <input type="hidden" name="crop_name_cd" id="deleteId">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
                $('#tblUser').DataTable();

                $('#editModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var cropNameCd = button.data('id');
                    var cropName = button.data('crop-name');
                    var cropNameAs = button.data('crop-name-as');
                    var registryNo = button.data('registry-no');
                    var scientificName = button.data('scientific-name');

                    var modal = $(this);
                    modal.find('#cropNameCd').val(cropNameCd);
                    modal.find('#cropName').val(cropName);
                    modal.find('#cropNameAs').val(cropNameAs);
                    modal.find('#registryNo').val(registryNo);
                    modal.find('#scientificName').val(scientificName);
                });

                $('#editForm').on('submit', function(e) {
                    e.preventDefault(); // Prevent form submission

                    var isValid = true;
                    var cropName = $('#cropName').val().trim();
                    var registryNo = $('#registryNo').val().trim();
                    var cropNameCd = $('#cropNameCd').val().trim();
                    var form = $(this);

                    // Clear previous error states
                    $('#cropName').removeClass('is-invalid');
                    $('#registryNo').removeClass('is-invalid');
                    $('.invalid-feedback').hide();

                    // Check Crop Name field
                    if (cropName === '') {
                        $('#cropName').addClass('is-invalid');
                        $('.invalid-feedback').filter('.crop-name-feedback').show();
                        isValid = false;
                    }

                    // Perform AJAX check for duplicate Crop Registry Number only if it's not empty
                    if (registryNo !== '') {
                        $.ajax({
                            url: '{{ route('admin.appmaster.crops.checkRegistryNo') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                registry_no: registryNo,
                                crop_name_cd: cropNameCd
                            },
                            success: function(response) {
                                if (response.exists) {
                                    $('#registryNo').addClass('is-invalid');
                                    $('.invalid-feedback').filter('.registry-no-feedback').text(
                                        'The Crop Registry No is already in use.').show();
                                    isValid = false;
                                } else {
                                    $('#registryNo').removeClass('is-invalid');
                                    $('.invalid-feedback').filter('.registry-no-feedback').hide();
                                }

                                // If validation is successful, submit the form
                                if (isValid) {
                                    form.off('submit').submit(); // Allow form submission
                                }
                            },
                            error: function(xhr) {
                                console.error('AJAX Error:', xhr); // Debugging output
                            }
                        });
                    } else {
                        // If the registry number is empty, submit the form directly
                        if (isValid) {
                            form.off('submit').submit(); // Allow form submission
                        }
                    }
                });

                $('#deleteModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var crop_name_cd = button.data('crop_name_cd');

                    var modal = $(this);
                    modal.find('#deleteId').val(crop_name_cd);
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
