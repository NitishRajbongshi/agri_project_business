@extends('admin.common.layout')

@section('title', 'Symptom Management')

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
            <h5 class="card-header">Symptom Management</h5>
            <div>
                <a href="{{ route('admin.appmaster.createsymptom') }}" class="btn btn-outline-success">
                    <i class="tf-icons bx bx-plus-medical"></i>Add New Symptom
                </a>
            </div>
        </div>

        <div class="px-4 py-2">
            <div class="row">
                <div class="col-md-4">
                    <label for="crop_type_cd" class="form-label">Select Crop Type</label>
                    <select class="form-select" id="crop_type_cd">
                        <option value="">Select Crop Type</option>
                        @foreach ($cropTypes as $item)
                            <option value="{{ $item->crop_type_cd }}">{{ $item->crop_type_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="crop_type_cd" class="form-label">Select Crop Name</label>
                    <select class="form-select" id="crop_type_cd">
                        <option value="">Select</option>
                        @foreach ($cropNames as $item)
                            <option value="{{ $item->crop_name_cd }}">{{ $item->crop_name_desc }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class="col-md-4">
                    <label for="disease_cd" class="form-label">Select Disease</label>
                    <select class="form-select" id="disease_cd">
                        <option value="">Select Disease</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row my-1 justify-content-end align-item-center">
            <div class="col-sm-6 col-md-3 d-flex justify-content-end my-1">
                <span class="spn_crop_symptom"></span>
            </div>
        </div>
        <div class="text-xs px-4" id="tableContainer" style="display: none;">
            <table class="table-responsive text-xs table table-bordered " id="tblCropSymptoms">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Symptom</th>
                        <th>Disease</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="tableBody">
                    <!-- Table rows will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Symptom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="{{ route('admin.appmaster.symptom.update') }}"
                    autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="symptom" class="form-label">Symptom</label>
                            <textarea rows="4" class="form-control" id="symptom" name="symptom" placeholder="Enter symptom"></textarea>
                            <div class="invalid-feedback symptom-feedback" style="display: none;">
                                Please provide a Symptom.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="disease_cd" class="form-label">Select Disease</label>
                            <select class="form-select @error('disease_cd') is-invalid @enderror" id="disease_cd_edit"
                                name="disease_cd">
                                <option value="">Select Disease</option>
                                @foreach ($diseases as $id => $desc)
                                    <option value="{{ $id }}">{{ $desc }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback disease-feedback" style="display: none;">
                                Please select a Disease.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="language_cd" class="form-label">Select Language</label>
                            <select class="form-select @error('language_cd') is-invalid @enderror" id="language_cd"
                                name="language_cd">
                                <option value="">Select Language</option>
                                @foreach ($languages as $code => $description)
                                    <option value="{{ $code }}">{{ $description }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback language-feedback" style="display: none;">
                                Please select a Language.
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Symptom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="{{ route('admin.appmaster.symptom.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this symptom?</p>
                        <input type="hidden" name="id" id="deleteId">
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
<script type="module" src="{{ asset('admin_assets/js/admin/cropSymptomsMaster.js') }}"></script>
    <script>
        var diseases = @json($diseases);

        var symptoms = @json($symptoms);
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
    </script>
@endsection
