@extends('admin.common.layout')
@section('title', 'User Role Management')
@section('custom_header')
@endsection
@section('main')
    @if ($message = Session::get('success'))
        <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-2">
        <div class="m-4">
            <div class="d-flex flex-wrap justify-content-between">
                <div class="col-12 col-md-6 text-sm">
                    <h5 class="text-underline text-md">Customer Details</h5>
                    <p class="card-text">
                        Booking ID:
                        <span class="text-secondary">
                            {{ $booking_id }}
                        </span>
                    </p>
                    <p class="card-text">
                        Customer Name:
                        <span class="text-secondary">
                            {{ $cust_name }}
                        </span>
                    </p>
                    <p class="card-text">
                        Customer Phone:
                        <span class="text-secondary">
                            +91 {{ $cust_phone }}
                        </span>
                    </p>
                </div>
                <div class="col-12 col-md-6 text-sm">
                    <h5 class="text-underline text-md">Ordered Item List</h5>
                    <form id="itemForm">
                        <ul class="list-group" style="list-style-type: none;">
                            @foreach ($priceList as $item)
                                <li class="list-group-item">
                                    <label>
                                        <input type="checkbox" class="item-checkbox" data-item-cd="{{ $item['item_cd'] }}" data-name="{{ $item['item_name'] }}"
                                            data-quantity="{{ $item['item_quantity'] }}"
                                            data-qty-unit="{{ $item['qty_unit'] }}"
                                            data-price-per-kg="{{ $item['price_per_kg'] }}"
                                            data-total-price="{{ $item['total_price'] }}">
                                        {{ $item['item_name'] }} ({{ $item['item_quantity'] }} {{ $item['qty_unit'] }})
                                        - Price: Rs. {{ $item['total_price'] }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                        <div style="text-align: right;">
                            <button type="button" class="my-2 btn btn-primary" id="calculateBill">Calculate Bill</button>
                            <button type="button" id="markDelivered" class="my-2 btn btn-warning">Mark as
                                Delivered</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card my-2 d-none" id="billDetails">
        <div class="m-4">
            <div>
                <div class="mb-2 text-md">
                    <span class="mr-2"><strong>Booking ID:</strong> {{ $booking_id }}</span>
                    <span><strong>Customer Name:</strong> {{ $cust_name }}</span>
                </div>

                <table id="billTable" class="table table-striped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th class="p-1 text-start">Item Name</th>
                            <th class="p-1 text-end pe-3">Quantity</th>
                            <th class="p-1 text-end pe-3">Price Per Kg</th>
                            <th class="p-1 text-end pe-3">Total Price</th>
                        </tr>
                    </thead>
                    <tbody id="billTableBody">
                    </tbody>
                    <tfoot>
                        <tr class="">
                            <th colspan="3" class="p-1 text-end pe-3 text-sm">Total Amount</th>
                            <th id="totalAmount" class="p-1 text-end pe-3 text-sm">Rs. 0</th>
                        </tr>
                    </tfoot>
                </table>
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
            $('#calculateBill').on('click', function() {
                let totalAmount = 0;
                const billTableBody = $('#billTableBody');
                billTableBody.empty(); // Clear previous entries
                $('.item-checkbox:checked').each(function() {
                    const name = $(this).data('name');
                    const quantity = $(this).data('quantity');
                    const qtyUnit = $(this).data('qty-unit');
                    const pricePerKg = $(this).data('price-per-kg');
                    const totalPrice = $(this).data('total-price');

                    totalAmount += parseFloat(totalPrice);

                    // Append row to the table
                    billTableBody.append(`
                        <tr class="text-center">
                            <td class="p-1 text-start">${name}</td>
                            <td class="p-1 text-end pe-3">${quantity} ${qtyUnit}</td>
                            <td class="p-1 text-end pe-3">Rs. ${pricePerKg.toFixed(2)}</td>
                            <td class="p-1 text-end pe-3">Rs. ${totalPrice.toFixed(2)}</td>
                        </tr>
                    `);
                });

                // Update total amount
                $('#totalAmount').text(`Rs. ${totalAmount.toFixed(2)}`);
                // Show the bill details section
                $('#billDetails').removeClass('d-none');
            });

            // Mark as Delivered
            $('#markDelivered').on('click', function() {
                const selectedItems = [];
                $('.item-checkbox:checked').each(function() {
                    selectedItems.push($(this).data('item-cd'));
                });

                if (selectedItems.length === 0) {
                    alert('Please select at least one item to mark as delivered.');
                    return;
                }
                console.log(selectedItems);
                $.ajax({
                    url: "{{ route('order.delivered') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        booking_id: "{{ $booking_id }}",
                        item_cds: selectedItems,
                    },
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(error) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            $('#calculateBill').on('click', function() {
                let totalPrice = 0;
                $('.item-checkbox:checked').each(function() {
                    totalPrice += parseFloat($(this).data('price'));
                });
                $('#totalPrice').text(totalPrice.toFixed(2));
            });

            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var bookingID = button.data('booking-id');
                var customerName = button.data('customer-name');
                console.log(customerName);

                // var email = button.data('email');
                // var address = button.data('address');
                // var roles = button.data('roles').split(',').map(role => role.trim());

                var modal = $(this);
                modal.find('#book_id_edit').text(bookingID);
                modal.find('#customer_name_edit').text(customerName);
                // modal.find('#modalEmail').text(email);
                // modal.find('#modalAddress').text(address);

                // // Ensure Role F is always checked
                // modal.find('input[type="checkbox"]').prop('checked', false);
                // modal.find('input[type="checkbox"][disabled]').prop('checked', true);

                // roles.forEach(function(role) {
                //     var checkbox = modal.find('#role_' + role);
                //     if (checkbox.length) {
                //         checkbox.prop('checked', true);
                //     }
                // });
            });
        });
    </script>
@endsection
