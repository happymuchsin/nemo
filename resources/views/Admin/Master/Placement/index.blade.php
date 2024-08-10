@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Placement'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>NIK / Username</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th>Position</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Type'" :id="'reff'">
                <x-slot:option>
                    <option value="line">Line</option>
                    <option value="counter">Counter</option>
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Location'" :id="'lokasi'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            $('#reff').select2({
                placeholder: "Select Type",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            $('#reff').on('change', function() {
                $.ajax({
                    url: "{{ route('admin.master.placement.spinner') }}",
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        reff: $(this).val(),
                    },
                    success: function(data) {
                        $('#lokasi').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#lokasi').append('<option value="' + v.id + '">' + v.area
                                .name + ' - ' + v.name + '</option>')
                        })
                    }
                })
            });
            $('#lokasi').select2({
                placeholder: "Select Location",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * 0.6,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('admin.master.placement.data') }}",
                },
                columns: [{
                        data: 'username'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'division'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'lokasi'
                    },
                    {
                        data: 'action'
                    },
                ],
                order: [],
                paging: false,
            });
        })

        function crup() {
            if ($('#lokasi').val() == '') {
                Swal.fire('Warning!', 'Please select Location', 'warning');
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.master.placement.crup') }}",
                    data: {
                        'id': $('#key').val(),
                        'reff': $('#reff').val(),
                        'lokasi': $('#lokasi').val(),
                    },
                    beforeSend: function() {
                        Swal.fire({
                            iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                            title: 'Please Wait',
                            html: 'Fetching your data..',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                        Swal.showLoading();
                    },
                    complete: function() {
                        // Swal.close();
                    },
                    success: function(response) {
                        $('#crupModal').modal('toggle');
                        Swal.fire('Success!', response, 'success');
                        setTimeout(() => {
                            Swal.close();
                        }, 1000);
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Warning!', response.responseText, 'warning');
                    }
                })
            }
        };

        function edit(url) {
            $.ajax({
                    type: "get",
                    url: url,
                    beforeSend: function() {
                        Swal.fire({
                            iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                            title: 'Please Wait',
                            html: 'Fetching your data..',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                        Swal.showLoading();
                    },
                    complete: function() {
                        Swal.close();
                    },
                })
                .done(function(response) {
                    $('#crupJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Placement ' + response.username +
                        ' - ' + response.name + '</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#reff').val(response.reff).trigger('change');
                    setTimeout(() => {
                        $('#lokasi').val(response.lokasi).trigger('change');
                    }, 500);
                    $('#key').val(response.id);
                    $('#crupModal').modal('toggle');
                });
        }

        function hapus(url) {
            Swal.fire({
                icon: 'question',
                title: 'Are you sure want to permanent Delete this Data?',
                showCancelButton: true,
                confirmButtonText: 'Confirm Delete',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: url,
                        type: "GET",
                        beforeSend: function() {
                            Swal.fire({
                                iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                                title: 'Please Wait',
                                html: 'Fetching your data..',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            });
                            Swal.showLoading();
                        },
                        complete: function() {
                            // Swal.close();
                        },
                        success: function(response) {
                            Swal.fire('Success!', response, 'success');
                            setTimeout(() => {
                                Swal.close();
                            }, 1000);
                            table.ajax.reload();
                        },
                        error: function(response) {
                            Swal.fire('Warning!', response.responseText, 'warning');
                        }
                    });
                }
            })
        }
    </script>
@endsection
