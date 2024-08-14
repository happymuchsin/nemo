@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Role'">
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

    <x-modal.modal :name="'detail'" :ukuran="'modal-xl'">
        <x-slot:body>
            <input type="hidden" id="role_id">
            <x-layout.table :id="'tableDetail'">
                <x-slot:thead>
                    <tr>
                        <th>Permission</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-modal.modal>

    <script>
        var table = null,
            tableDetail = null;
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
                    url: "{{ route('admin.tools.role.data') }}",
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
                    url: "{{ route('admin.tools.role.crup') }}",
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
                        Swal.fire('Warning!', response.responseText, 'warning');
                    }
                })
            }
        }

        function detail(url) {
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
                    $('#detailJudul').html('<h5 class="modal-title"><i class="fa fa-file-pen"></i> Detail Role ' +
                        response.name + '</h5>');
                    $('#detailHeader').removeClass('bg-success');
                    $('#detailHeader').addClass('bg-info');

                    if ($.fn.DataTable.isDataTable("#tableDetail")) {
                        $('#tableDetail').html('');
                        $('#tableDetail').DataTable().clear().destroy();
                    }

                    setTimeout(() => {
                        tableDetail = $('#tableDetail').DataTable({
                            dom: '<"toolbarDetail">frtip',
                            scrollY: screen.height * 0.4,
                            scrollX: true,
                            scrollCollapse: true,
                            paging: false,
                            ajax: {
                                url: "{{ route('admin.tools.role.data-permission') }}",
                                data: function(d) {
                                    d.role_id = $('#role_id').val();
                                }
                            },
                            columns: [{
                                    data: 'ket',
                                },
                                {
                                    data: 'action',
                                }
                            ],
                        });
                        $('div.toolbarDetail').html(
                            '<div><label>Permission</label><div class="form-group" onchange="crupPermission()"><select name="permission" id="permission" class="form-select" required></select></div></div>'
                        );
                        $('#permission').select2({
                            placeholder: "Select Permission",
                            dropdownParent: $('#detailModal'),
                            width: '100%',
                            ajax: {
                                url: "{{ route('admin.tools.role.spinner') }}",
                                dataType: 'json',
                                data: function(params) {
                                    const query = {
                                        role_id: $('#role_id').val(),
                                        q: $.trim(params.term)
                                    };
                                    return query;
                                },
                                processResults: function(data) {
                                    return {
                                        results: $.map(data, function(item) {
                                            return {
                                                text: item.description,
                                                id: item.id,
                                            };
                                        }),
                                    };
                                },
                                cache: false
                            }
                        });
                    }, 250);

                    $('#role_id').val(response.role_id);

                    $("#detailModal").modal('toggle');
                });
        }

        function crupPermission() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('admin.tools.role.crup-permission') }}",
                data: {
                    'role_id': $('#role_id').val(),
                    'permission_id': $('#permission').val(),
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
                    Swal.fire('Success!', response, 'success');
                    setTimeout(() => {
                        Swal.close();
                    }, 1000);
                    tableDetail.ajax.reload();
                },
                error: function(response) {
                    Swal.fire('Warning!', response.responseText, 'warning');
                }
            })
        }

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-plus"></i> Input Role</h5>');
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
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit Role</h5>');
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
                            Swal.fire('Warning!', response.responseText, 'warning');
                        }
                    });
                }
            })
        }

        function hapusDetail(url) {
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
                            tableDetail.ajax.reload();
                        },
                        error: function(response) {
                            Swal.fire('Warning!', response.responseText, 'warning');
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

        .toolbarDetail {
            float: left;
        }
    </style>
@endsection
