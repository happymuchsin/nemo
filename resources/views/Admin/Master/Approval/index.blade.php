@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Approval'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot name="thead">
                    <tr>
                        <th>Approved By</th>
                        <th>Action</th>
                    </tr>
                </x-slot>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Approved By'" :id="'user_id'">
                <x-slot:option>
                    @foreach ($user as $d)
                        <option value="{{ $d->id }}">
                            {{ "$d->username - $d->name" }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
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
            initSelect('user_id', 'Select Approved By', 'crupModal');
            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.approval.data') }}",
                },
                columns: [{
                        data: 'user_id'
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
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fal fa-circle-plus" /></i> New</button>'
            );
        })

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fal fa-file-plus"></i> Input</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#user_id').val('').trigger('change');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#user_id').val() == '') {
                warningAlert('Please select Approved By');
            } else {
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.approval.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'user_id': $('#user_id').val(),
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
