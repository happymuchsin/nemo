@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Box'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Counter</th>
                        <th>Name</th>
                        <th>RFID</th>
                        <th>Type</th>
                        {{-- <th>Status</th> --}}
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Counter'" :id="'master_counter_id'">
                <x-slot:option>
                    @foreach ($counter as $d)
                        <option value="{{ $d->id }}">{{ $d->area->name . ' - ' . $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" />
            <x-modal.body :tipe="'text'" :label="'RFID'" :id="'rfid'" />
            <x-modal.body :tipe="'select'" :label="'Type'" :id="'tipe'">
                <x-slot:option>
                    <option value="NORMAL">NORMAL</option>
                    <option value="RETURN">RETURN</option>
                </x-slot:option>
            </x-modal.body>
            {{-- <x-modal.body :tipe="'select'" :label="'Status'" :id="'status'" /> --}}
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
            $('#master_counter_id').select2({
                placeholder: "Select Counter",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            $('#tipe').select2({
                placeholder: "Select Type",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            // $('#tipe').on('change', function() {
            //     $('#status').html('');
            //     if ($(this).val() == 'RETURN') {
            //         $('#status').append(`
        //             <option value=""></option>
        //             <option value="OK">OK</option>
        //             <option value="NG">NG</option>
        //         `);
            //     } else {
            //         $('#status').append(`
        //             <option value="NEW">NEW</option>
        //         `);
            //     }
            // });
            // $('#status').select2({
            //     placeholder: "Select Status",
            //     dropdownParent: $('#crupModal'),
            //     width: '100%',
            // });
            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * 0.6,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('admin.master.box.data') }}",
                },
                columns: [{
                        data: 'counter'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'rfid'
                    },
                    {
                        data: 'tipe'
                    },
                    // {
                    //     data: 'status'
                    // },
                    {
                        data: 'action'
                    },
                ],
                order: [
                    [0, 'asc']
                ],
                pageLength: 50,
                lengthMenu: [
                    [50, 100, 500, -1],
                    [50, 100, 500, "All"]
                ],
            });
            $('div.toolbar').html(
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fa fa-circle-plus" /></i> New</button>'
            );
        })

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-plus"></i> Input</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#master_counter_id').val('').trigger('change');
            $('#name').val('');
            $('#rfid').val('');
            $('#tipe').val('').trigger('change');
            // $('#status').val('').trigger('change');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#master_counter_id').val() == '') {
                Swal.fire('Warning!', 'Please select Counter', 'warning');
            } else if ($('#name').val() == '') {
                Swal.fire('Warning!', 'Please insert Name', 'warning');
            } else if ($('#tipe').val() == '') {
                Swal.fire('Warning!', 'Please select Type', 'warning');
            }
            // else if ($('#status').val() == '') {
            //     Swal.fire('Warning!', 'Please select Status', 'warning');
            // } 
            else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.master.box.crup') }}",
                    data: {
                        'id': $('#key').val(),
                        'master_counter_id': $('#master_counter_id').val(),
                        'name': $('#name').val(),
                        'rfid': $('#rfid').val(),
                        'tipe': $('#tipe').val(),
                        // 'status': $('#status').val(),
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
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#master_counter_id').val(response.master_counter_id).trigger('change');
                    $('#name').val(response.name);
                    $('#rfid').val(response.rfid);
                    $('#tipe').val(response.tipe).trigger('change');
                    // $('#status').val(response.status).trigger('change');
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
