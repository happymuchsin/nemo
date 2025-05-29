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
            initSelect('master_counter_id', 'Select Counter', 'crupModal');
            initSelect('tipe', 'Select Type', 'crupModal');
            table = initDataTable('table', '', '', '', {
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
            $('#master_counter_id').val('').trigger('change');
            $('#name').val('');
            $('#rfid').val('');
            $('#tipe').val('').trigger('change');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#master_counter_id').val() == '') {
                warningAlert('Please select Counter');
            } else if ($('#name').val() == '') {
                warningAlert('Please insert Name');
            } else if ($('#tipe').val() == '') {
                warningAlert('Please select Type');
            } else {
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.box.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'master_counter_id': $('#master_counter_id').val(),
                        'name': $('#name').val(),
                        'rfid': $('#rfid').val(),
                        'tipe': $('#tipe').val(),
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
                    $('#master_counter_id').val(response.master_counter_id).trigger('change');
                    $('#name').val(response.name);
                    $('#rfid').val(response.rfid);
                    $('#tipe').val(response.tipe).trigger('change');
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
