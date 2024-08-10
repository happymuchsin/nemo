@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Permission'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" :upper="false" />
            <x-modal.body :tipe="'text'" :label="'Description'" :id="'description'" :upper="false" />
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
            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * .6,
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    rightColumns: 1
                },
                ajax: {
                    url: "{{ route('admin.tools.permission.data') }}",
                },
                columns: [{
                        data: "name"
                    },
                    {
                        data: "description"
                    },
                    {
                        data: "action",
                        sortable: false,
                        ordering: false,
                    }
                ],
                order: [],
                paging: false,
            });
            $('div.toolbar').html(
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fa fa-circle-plus" /></i> New</button>'
            );
        })

        function crup() {
            if ($('#name').val() == '') {
                Swal.fire('Warning', 'Please insert Name', 'warning')
            } else if ($('#description').val() == '') {
                Swal.fire('Warning', 'Please insert Description', 'warning')
            } else {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.tools.permission.crup') }}",
                    data: {
                        'id': $('#key').val(),
                        'name': $('#name').val(),
                        'description': $('#description').val(),
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
                        Swal.fire('Warning!', response, 'warning');
                    }
                })
            }
        };

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-plus"></i> Input Permission</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#name').val('');
            $('#description').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

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
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit Permission</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#name').val(response.name);
                    $('#description').val(response.description);
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
                            Swal.fire('Warning!', response, 'warning');
                        }
                    });
                }
            })
        }
    </script>
    <style>
        .toolbar {
            float: left;
        }
    </style>
@endsection
