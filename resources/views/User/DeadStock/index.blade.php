@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Dead Stock'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :tipe="'select'" :label="'Area'" :id="'filter_area'" :colom="'col-sm-auto'">
                        <x-slot:option>
                            @foreach ($area as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Area</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Code</th>
                        <th>Machine</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'transfer'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="transferKey">
            <input type="hidden" id="transferMode">
            <h2 id="transferSubJudul"></h2>
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Area'" :id="'transfer_area'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Brand'" :id="'transfer_brand'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Tipe'" :id="'transfer_tipe'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Size'" :id="'transfer_size'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Code'" :id="'transfer_code'" :readonly="'readonly'" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <x-modal.body :tipe="'text'" :label="'Machine'" :id="'transfer_machine'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-4">
                    <x-modal.body :tipe="'text'" :label="'Remaining Stock'" :id="'transfer_stock'" :readonly="'readonly'" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <h3>Counter List</h3>
                    <x-layout.table :id="'tableCounter'">
                        <x-slot:thead>
                        </x-slot:thead>
                    </x-layout.table>
                </div>
                <div class="col-sm-6">
                    <h3>History Transfer</h3>
                    <x-layout.table :id="'tableTransfer'">
                        <x-slot:thead>
                            <tr>
                                <th>Date Time</th>
                                <th>Counter</th>
                                <th>Box</th>
                                <th>Qty</th>
                            </tr>
                        </x-slot:thead>
                    </x-layout.table>
                </div>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'transfer()'" :icon="'fa fa-save'" :name="'Transfer'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null,
            tableCounter = null,
            tableTransfer = null,
            collectMasterNeedleId = [];
        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('user.dead-stock.data') }}",
                    data: function(d) {
                        d.filter_area = $('#filter_area').val();
                        d.tipe = 'data';
                    },
                },
                columns: [{
                        data: 'area'
                    },
                    {
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
                        data: 'in'
                    },
                    {
                        data: 'out'
                    },
                    {
                        data: 'stock'
                    },
                    {
                        data: 'action'
                    },
                ],
                rowCallback: function(row, data) {
                    if (data.stock < data.min_stock) {
                        $(row).css('color', 'red');
                    }
                }
            });
        })

        function btnTransfer(url, mode) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    if (mode == 'add') {
                        $('#transferJudul').html('<h5 class="modal-title"><i class="fa fa-plus"></i> Add</h5>');
                        $('#transferSubJudul').text('FROM COUNTER TO DEAD STOCK');
                        $('#transferHeader').removeClass('bg-warning');
                        $('#transferHeader').addClass('bg-success');
                    } else if (mode == 'minus') {
                        $('#transferJudul').html('<h5 class="modal-title"><i class="fa fa-minus"></i> Minus</h5>');
                        $('#transferSubJudul').text('FROM DEAD STOCK TO COUNTER');
                        $('#transferHeader').removeClass('bg-success');
                        $('#transferHeader').addClass('bg-warning');
                    }
                    $('#transfer_area').val(response.area.name);
                    $('#transfer_brand').val(response.needle.brand);
                    $('#transfer_tipe').val(response.needle.tipe);
                    $('#transfer_size').val(response.needle.size);
                    $('#transfer_code').val(response.needle.code);
                    $('#transfer_machine').val(response.needle.machine);
                    $('#transfer_stock').val(response.in - response.out);
                    $('#transferKey').val(response.id);
                    $('#transferMode').val(mode);

                    if ($.fn.DataTable.isDataTable("#tableCounter")) {
                        $('#tableCounter').html('');
                        $('#tableCounter').DataTable().clear().destroy();
                    }

                    setTimeout(() => {
                        $('#tableCounterHead').html(`
                            <tr>
                                <th>Counter</th>
                                <th>Box</th>
                                <th style="width: 100px">Stock</th>
                                <th style="width: 100px">Transfer</th>
                                <th style="width: 100px">After</th>
                            </tr>
                        `);
                        tableCounter = initDataTable('tableCounter', 'toolbarCounter', '', 0.4, {
                            ajax: {
                                url: "{{ route('user.dead-stock.data') }}",
                                data: function(d) {
                                    d.dead_stock_id = response.id;
                                    d.tipe = 'counter';
                                    d.mode = mode;
                                }
                            },
                            columns: [{
                                    data: 'counter'
                                },
                                {
                                    data: 'box'
                                },
                                {
                                    data: 'stock'
                                },
                                {
                                    data: 'transfer'
                                },
                                {
                                    data: 'after'
                                },
                            ],
                            paging: false,
                            drawCallback: function() {
                                $('.input-transfer').each(function() {
                                    var id = $(this).attr('id');
                                    $('#transfer' + id).on('input', function() {
                                        if (mode == 'add') {
                                            $('#after' + id).val(+$($('#stock' + id)).val() - +$('#transfer' + id).val());
                                        } else if (mode == 'minus') {
                                            $('#after' + id).val(+$($('#transfer' + id)).val() + +$('#stock' + id).val());
                                        }
                                    });
                                })
                            },
                        });
                    }, 250);

                    if ($.fn.DataTable.isDataTable("#tableTransfer")) {
                        $('#tableTransfer').html('');
                        $('#tableTransfer').DataTable().clear().destroy();
                    }

                    setTimeout(() => {
                        tableTransfer = initDataTable('tableTransfer', 'toolbarTransfer', '', 0.4, {
                            ajax: {
                                url: "{{ route('user.dead-stock.history') }}",
                                data: function(d) {
                                    d.dead_stock_id = response.id;
                                    d.tipe = 'transfer';
                                    d.mode = mode;
                                }
                            },
                            columns: [{
                                    data: 'created_at'
                                },
                                {
                                    data: 'counter'
                                },
                                {
                                    data: 'box'
                                },
                                {
                                    data: 'qty'
                                },
                            ],
                            paging: false
                        });
                    }, 250);
                    $('#transferModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function transfer() {
            customAlert({
                icon: 'question',
                title: 'Is the data correct?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#08fe3e',
                callback: function() {
                    var data = [],
                        total = 0,
                        mode = $('#transferMode').val(),
                        z = 0;
                    $('.input-transfer').each(function() {
                        var id = $(this).attr('id');
                        if ($('#transfer' + id).val() > 0 || $('#transfer' + id).val() != '') {
                            data.push({
                                'counter_id': id,
                                'stock': $('#stock' + id).val(),
                                'after': $('#after' + id).val(),
                                'qty': $('#transfer' + id).val(),
                            })
                            if (mode == 'add') {
                                if ($('#after' + id).val() < 0) {
                                    z = 1;
                                    return false;
                                }
                            }
                            total += +$('#transfer' + id).val();
                        }
                    })
                    if (z == 1) {
                        warningAlert('Transfer is more than Stock');
                        return;
                    }
                    if (total == 0) {
                        warningAlert('Please insert Qty Transfer');
                        return;
                    } else {
                        if (mode == 'minus') {
                            if (total > $('#transfer_stock').val()) {
                                warningAlert('Total Transfer is more than Remaining Stock');
                                return;
                            }
                        }
                        sendAjax('transferModal', {
                            url: "{{ route('user.dead-stock.transfer') }}",
                            type: "POST",
                            data: {
                                mode: mode,
                                dead_stock_id: $('#transferKey').val(),
                                data: data,
                            },
                            success: function(response) {
                                $('#transferModal').modal('toggle');
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
                }
            })
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
