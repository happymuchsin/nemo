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
            table = initDataTable('table', '', '', '', {
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
                paging: false,
            });
            $('div.toolbar').html(
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fa fa-circle-plus" /></i> New</button>'
            );
        })

        function crup() {
            if ($('#name').val() == '') {
                warningAlert('Please insert Name');
            } else if ($('#description').val() == '') {
                warningAlert('Please insert Description');
            } else {
                sendAjax('crupModal', {
                    url: "{{ route('admin.tools.permission.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'name': $('#name').val(),
                        'description': $('#description').val(),
                    },
                    success: function(response) {
                        $('#crupModal').modal('toggle');
                        successAlert(response);
                        closeAlert();
                        setTimeout(() => {
                            table.ajax.reload();
                        }, 1000);
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
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
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
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function hapus(url) {
            customAlert({
                icon: 'question',
                title: "Are you sure want to permanent Delete this Data?",
                showCancelButton: true,
                confirmButtonText: "Confirm Delete",
                confirmButtonColor: '#dc3545',
                cancelButtonText: "Cancel",
                callback: function() {
                    sendAjax('', {
                        url: url,
                        type: "GET",
                        success: function(response) {
                            successAlert(response);
                            closeAlert();
                            setTimeout(() => {
                                table.ajax.reload();
                            }, 1000);
                        },
                        error: function(response) {
                            warningAlert(response.responseText);
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
