@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Status'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.status.data') }}",
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'action'
                    },
                ],
                order: [
                    [0, 'asc']
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
            $('#name').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#name').val() == '') {
                warningAlert('Please insert Name');
            } else {
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.status.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'name': $('#name').val(),
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
                        warningAlert(response.responseText);
                    }
                })
            }
        };

        function edit(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#crupJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#name').val(response.name);
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
@endsection
