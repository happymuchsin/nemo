@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Monthly Stock'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'month'" :label="'Month'" :id="'filter_month'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Code</th>
                        <th>Machine</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'month'" :label="'Month'" :id="'month'" />
            <x-modal.body :tipe="'select'" :label="'Needle'" :id="'master_needle_id'">
                <x-slot:option>
                    @foreach ($master_needle as $d)
                        <option value="{{ $d->id }}">{{ $d->brand }} - {{ $d->tipe }} - {{ $d->size }} - {{ $d->code }} - {{ $d->machine }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'number'" :label="'Min Stock'" :id="'min_stock'" :min="'1'" />
            <x-modal.body :tipe="'number'" :label="'Max Stock'" :id="'max_stock'" :min="'1'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            $('#filter_month').val("{{ date('Y-m') }}").trigger('change');

            initSelect('master_needle_id', 'Select Needle', 'crupModal');

            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.monthly-stock.data') }}",
                    data: function(d) {
                        d.filter_month = $('#filter_month').val();
                    },
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
                        data: 'max_stock'
                    },
                    {
                        data: 'action'
                    },
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

            $('#month').val('').trigger('change');
            $('#master_needle_id').val('').trigger('change');
            $('#min_stock').val('');
            $('#max_stock').val('');

            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#month').val() == '') {
                warningAlert('Please select Month');
            } else if ($('#master_needle_id').val() == '') {
                warningAlert('Please select Needle');
            } else if ($('#min_stock').val() == '') {
                warningAlert('Please insert Min Stock');
            } else if ($('#max_stock').val() == '') {
                warningAlert('Please insert Max Stock');
            } else {
                customAlert({
                    icon: 'question',
                    title: 'Is the data correct?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#08fe3e',
                    callback: function() {
                        sendAjax('crupModal', {
                            url: "{{ route('admin.master.monthly-stock.crup') }}",
                            type: "POST",
                            data: {
                                id: $('#key').val(),
                                month: $('#month').val(),
                                master_needle_id: $('#master_needle_id').val(),
                                min_stock: $('#min_stock').val(),
                                max_stock: $('#max_stock').val(),
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

                    $('#month').val(response.tahun + '-' + response.bulan).trigger('change');
                    $('#master_needle_id').val(response.master_needle_id).trigger('change');
                    $('#min_stock').val(response.min_stock);
                    $('#max_stock').val(response.max_stock);

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
