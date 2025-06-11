@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Warehouse'">
        <x-slot:body>
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

    <x-modal.modal :name="'store'">
        <x-slot:body>
            <x-modal.body :tipe="'select'" :label="'Area'" :id="'master_area_id'">
                <x-slot:option>
                    @foreach ($area as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Needle Category'" :id="'needle_category'" :defaultOption="false">
                <x-slot:option>
                    <option value="all">All</option>
                    <option value="single">Single Needle</option>
                    <option value="double">Double Needle</option>
                    <option value="obras">Obras</option>
                    <option value="kansai">Kansai</option>
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Needle'" :id="'master_needle_id'" />
            <x-modal.body :tipe="'number'" :label="'Qty'" :id="'qty'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'store()'" :icon="'fa fa-save'" :name="'Save'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'update'" :ukuran="'modal-xl'">
        <x-slot:body>
            <input type="hidden" id="editKey">
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Area'" :id="'edit_area'" :readonly="'readonly'" />
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Brand'" :id="'edit_brand'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Tipe'" :id="'edit_tipe'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Size'" :id="'edit_size'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Code'" :id="'edit_code'" :readonly="'readonly'" />
                </div>
            </div>
            <x-modal.body :tipe="'text'" :label="'Machine'" :id="'edit_machine'" :readonly="'readonly'" />
            <x-modal.body :tipe="'number'" :label="'Qty'" :id="'edit_qty'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'update(\'' . 'edit' . '\')'" :icon="'fa fa-save'" :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'add'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="addKey">
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Area'" :id="'add_area'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Brand'" :id="'add_brand'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Tipe'" :id="'add_tipe'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Size'" :id="'add_size'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Code'" :id="'add_code'" :readonly="'readonly'" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <x-modal.body :tipe="'text'" :label="'Machine'" :id="'add_machine'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-4">
                    <x-modal.body :tipe="'number'" :label="'Qty'" :id="'add_qty'" />
                </div>
            </div>
            <x-layout.table :id="'tableHistory'">
                <x-slot:thead>
                    <tr>
                        <th>Date Time</th>
                        <th>Before</th>
                        <th>Add Stock</th>
                        <th>After</th>
                        <th>User</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'update(\'' . 'add' . '\')'" :icon="'fa fa-save'" :name="'Add Stock'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'transfer'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="transferKey">
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
            tableStore = null,
            tableHistory = null,
            tableCounter = null,
            tableTransfer = null,
            collectMasterNeedleId = [];
        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            initSelect('master_area_id', 'Select Area', 'storeModal');
            initSelect('master_needle_id', 'Select Needle', 'storeModal');
            initSelect('needle_category', 'Select Category', 'storeModal');
            $('#needle_category').on('change', function() {
                sendAjax('storeModal', {
                    url: "{{ route('user.warehouse.spinner') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'needle',
                        needle_category: $(this).val(),
                    },
                    success: function(data) {
                        unwaitAlert();
                        $('#master_needle_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_needle_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            })
            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('user.warehouse.data') }}",
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
            $('div.toolbar').html(
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fal fa-circle-plus" /></i> New</button>'
            );
        })

        function add() {
            $('#storeJudul').html('<h5 class="modal-title"><i class="fal fa-file-plus"></i> Input</h5>');
            $('#storeHeader').addClass('bg-success');
            $('#storeHeader').removeClass('bg-info');

            $('#master_area_id').val('').trigger('change');
            $('#master_needle_id').val('').trigger('change');
            $('#needle_category').val('all').trigger('change');
            $('#qty').val('');
            $('#storeModal').modal('toggle');
        }

        function store() {
            customAlert({
                icon: 'question',
                title: 'Is the data correct?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#08fe3e',
                callback: function() {
                    if ($('#master_area_id').val() == '') {
                        warningAlert('Please select Area');
                    } else if ($('#master_needle_id').val() == '') {
                        warningAlert('Please select Needle');
                    } else if ($('#qty').val() == '') {
                        warningAlert('Please insert Qty');
                    } else {
                        sendAjax('storeModal', {
                            url: "{{ route('user.warehouse.store') }}",
                            type: "POST",
                            data: {
                                master_area_id: $('#master_area_id').val(),
                                master_needle_id: $('#master_needle_id').val(),
                                qty: $('#qty').val(),
                            },
                            success: function(response) {
                                $('#storeModal').modal('toggle');
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

        function btnTransfer(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#transferJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-right-left"></i> Transfer</h5>');
                    $('#transferHeader').removeClass('bg-success');
                    $('#transferHeader').addClass('bg-info');
                    $('#transfer_area').val(response.area.name);
                    $('#transfer_brand').val(response.needle.brand);
                    $('#transfer_tipe').val(response.needle.tipe);
                    $('#transfer_size').val(response.needle.size);
                    $('#transfer_code').val(response.needle.code);
                    $('#transfer_machine').val(response.needle.machine);
                    $('#transfer_stock').val(response.in - response.out);
                    $('#transferKey').val(response.id);

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
                                url: "{{ route('user.warehouse.data') }}",
                                data: function(d) {
                                    d.warehouse_id = response.id;
                                    d.tipe = 'counter';
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
                                        $('#after' + id).val(+$($('#transfer' + id)).val() + +$('#stock' + id).val());
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
                                url: "{{ route('user.warehouse.history') }}",
                                data: function(d) {
                                    d.warehouse_id = response.id;
                                    d.tipe = 'transfer';
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
                        total = 0;
                    $('.input-transfer').each(function() {
                        var id = $(this).attr('id');
                        if ($('#transfer' + id).val() > 0 || $('#transfer' + id).val() != '') {
                            data.push({
                                'ke_id': id,
                                'stock': $('#stock' + id).val(),
                                'after': $('#after' + id).val(),
                                'qty': $('#transfer' + id).val(),
                            })
                            total += +$('#transfer' + id).val();
                        }
                    })
                    if (total == 0) {
                        warningAlert('Please insert Qty Transfer');
                    } else if (total > $('#transfer_stock').val()) {
                        warningAlert('Total Transfer is more than Remaining Stock');
                    } else {
                        sendAjax('transferModal', {
                            url: "{{ route('user.warehouse.transfer') }}",
                            type: "POST",
                            data: {
                                dari_id: $('#transferKey').val(),
                                dari: 'warehouse',
                                ke: 'stock',
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

        function edit(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#updateJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#updateHeader').removeClass('bg-success');
                    $('#updateHeader').addClass('bg-info');
                    $('#edit_area').val(response.area.name);
                    $('#edit_brand').val(response.needle.brand);
                    $('#edit_tipe').val(response.needle.tipe);
                    $('#edit_size').val(response.needle.size);
                    $('#edit_code').val(response.needle.code);
                    $('#edit_machine').val(response.needle.machine);
                    $('#edit_qty').val(response.in);
                    $('#editKey').val(response.id);
                    $('#updateModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function addWarehouse(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#addJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-plus"></i> Add Stock</h5>');
                    $('#addHeader').removeClass('bg-success');
                    $('#addHeader').addClass('bg-info');
                    $('#add_area').val(response.area.name);
                    $('#add_brand').val(response.needle.brand);
                    $('#add_tipe').val(response.needle.tipe);
                    $('#add_size').val(response.needle.size);
                    $('#add_code').val(response.needle.code);
                    $('#add_machine').val(response.needle.machine);
                    $('#add_qty').val(0);
                    $('#addKey').val(response.id);

                    if ($.fn.DataTable.isDataTable("#tableHistory")) {
                        $('#tableHistory').html('');
                        $('#tableHistory').DataTable().clear().destroy();
                    }

                    setTimeout(() => {
                        tableHistory = initDataTable('tableHistory', 'toolbarHistory', '', 0.4, {
                            ajax: {
                                url: "{{ route('user.warehouse.history') }}",
                                data: function(d) {
                                    d.warehouse_id = response.id;
                                    d.tipe = 'add';
                                }
                            },
                            columns: [{
                                    data: 'created_at'
                                },
                                {
                                    data: 'warehouse_before'
                                },
                                {
                                    data: 'qty'
                                },
                                {
                                    data: 'warehouse_after'
                                },
                                {
                                    data: 'user'
                                },
                            ],
                            paging: false
                        });
                    }, 250);
                    $('#addModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function update(tipe) {
            var modal = '';
            if (tipe == 'edit')
                modal = 'updateModal';
            else
                modal = 'addModal';
            sendAjax(modal, {
                url: "{{ route('user.warehouse.update') }}",
                type: "POST",
                data: {
                    id: tipe == 'edit' ? $('#editKey').val() : $('#addKey').val(),
                    tipe: tipe,
                    in: tipe == 'edit' ? $('#edit_qty').val() : $('#add_qty').val(),
                },
                success: function(response) {
                    if (tipe == 'edit')
                        $('#updateModal').modal('toggle');
                    else
                        $('#addModal').modal('toggle');
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

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
