@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Needle'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Code</th>
                        <th>Machine</th>
                        <th>Min Stock</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'text'" :label="'Brand'" :id="'brand'" />
            <x-modal.body :tipe="'text'" :label="'Type'" :id="'tipe'" />
            <x-modal.body :tipe="'text'" :label="'Size'" :id="'size'" />
            <x-modal.body :tipe="'text'" :label="'Code'" :id="'code'" />
            <x-modal.body :tipe="'text'" :label="'Machine'" :id="'machine'" />
            <x-modal.body :tipe="'number'" :label="'Min Stock'" :id="'min_stock'" :min="'1'" />
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
                ajax: {
                    url: "{{ route('admin.master.needle.data') }}",
                },
                columns: [{
                        data: 'brand'
                    },
                    {
                        data: 'tipe'
                    },
                    {
                        data: 'size'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'machine'
                    },
                    {
                        data: 'min_stock'
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
            $('#brand').val('');
            $('#tipe').val('');
            $('#size').val('');
            $('#code').val('');
            $('#machine').val('');
            $('#min_stock').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#brand').val() == '') {
                warningAlert('Please insert Brand');
            } else if ($('#tipe').val() == '') {
                warningAlert('Please insert Type');
            } else if ($('#size').val() == '') {
                warningAlert('Please insert Size');
            } else if ($('#code').val() == '') {
                warningAlert('Please insert Code');
            } else if ($('#machine').val() == '') {
                warningAlert('Please insert Machine');
            } else if ($('#min_stock').val() == '') {
                warningAlert('Please insert Min Stock');
            } else {
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.needle.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'brand': $('#brand').val(),
                        'tipe': $('#tipe').val(),
                        'size': $('#size').val(),
                        'code': $('#code').val(),
                        'machine': $('#machine').val(),
                        'min_stock': $('#min_stock').val(),
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
                    $('#brand').val(response.brand);
                    $('#tipe').val(response.tipe);
                    $('#size').val(response.size);
                    $('#code').val(response.code);
                    $('#machine').val(response.machine);
                    $('#min_stock').val(response.min_stock);
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
