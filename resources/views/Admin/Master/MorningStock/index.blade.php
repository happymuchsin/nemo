@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Morning Stock'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Code</th>
                        <th>Machine</th>
                        <th>Value</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Needle'" :id="'master_needle_id'">
                <x-slot:option>
                    @foreach ($master_needle as $d)
                        <option value="{{ $d->id }}">{{ $d->brand }} - {{ $d->tipe }} - {{ $d->size }} - {{ $d->code }} - {{ $d->machine }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'number'" :label="'Value'" :id="'value'" :min="'1'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            initSelect('master_needle_id', 'Select Needle', 'crupModal');

            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.morning-stock.data') }}",
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
                        data: 'value'
                    },
                    {
                        data: 'action'
                    },
                ],
            });
        })

        function crup() {
            if ($('#master_needle_id').val() == '') {
                warningAlert('Please select Needle');
            } else if ($('#value').val() == '') {
                warningAlert('Please insert Value');
            } else {
                customAlert({
                    icon: 'question',
                    title: 'Is the data correct?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#08fe3e',
                    callback: function() {
                        sendAjax('crupModal', {
                            url: "{{ route('admin.master.morning-stock.crup') }}",
                            type: "POST",
                            data: {
                                id: $('#key').val(),
                                value: $('#value').val(),
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
                })
            }
        }

        function edit(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();

                    $('#master_needle_id').prop('disabled', true);
                    $('#master_needle_id').val(response.id).trigger('change');
                    $('#value').val(response.value);

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
