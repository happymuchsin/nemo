@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Stock'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Area</th>
                        <th>Counter</th>
                        <th>Box</th>
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
            <x-modal.body :tipe="'select'" :label="'Counter'" :id="'master_counter_id'" />
            <x-modal.body :tipe="'select'" :label="'Box'" :id="'master_box_id'" />
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
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'store()'" :icon="'fa fa-save'"
                :name="'Save'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'update'" :ukuran="'modal-xl'">
        <x-slot:body>
            <input type="hidden" id="editKey">
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Area'" :id="'edit_area'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Counter'" :id="'edit_counter'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Box'" :id="'edit_box'" :readonly="'readonly'" />
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
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'update(\'' . 'edit' . '\')'" :icon="'fa fa-save'"
                :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'add'" :ukuran="'modal-xl'">
        <x-slot:body>
            <input type="hidden" id="addKey">
            <div class="row">
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Area'" :id="'add_area'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Counter'" :id="'add_counter'" :readonly="'readonly'" />
                </div>
                <div class="col">
                    <x-modal.body :tipe="'text'" :label="'Box'" :id="'add_box'" :readonly="'readonly'" />
                </div>
            </div>
            <div class="row">
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
            <x-modal.body :tipe="'text'" :label="'Machine'" :id="'add_machine'" :readonly="'readonly'" />
            <x-modal.body :tipe="'number'" :label="'Qty'" :id="'add_qty'" />
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
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'update(\'' . 'add' . '\')'" :icon="'fa fa-save'"
                :name="'Add Stock'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null,
            tableStore = null,
            tableHistory = null,
            collectMasterNeedleId = [];
        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            initSelect('master_area_id', 'Select Area', 'storeModal');
            initSelect('master_counter_id', 'Select Counter', 'storeModal');
            initSelect('master_box_id', 'Select Box', 'storeModal');
            initSelect('master_needle_id', 'Select Needle', 'storeModal');
            initSelect('needle_category', 'Select Category', 'storeModal');
            $('#master_area_id').on('change', function() {
                sendAjax('storeModal', {
                    url: "{{ route('user.stock.spinner') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'counter',
                        master_area_id: $(this).val(),
                    },
                    success: function(data) {
                        unwaitAlert();
                        $('#master_counter_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_counter_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            });
            $('#master_counter_id').on('change', function() {
                sendAjax('storeModal', {
                    url: "{{ route('user.stock.spinner') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'box',
                        master_area_id: $('#master_area_id').val(),
                        master_counter_id: $(this).val(),
                    },
                    success: function(data) {
                        unwaitAlert();
                        $('#master_box_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_box_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            });
            $('#needle_category').on('change', function() {
                sendAjax('storeModal', {
                    url: "{{ route('user.stock.spinner') }}",
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
                    url: "{{ route('user.stock.data') }}",
                    data: function(d) {
                        d.filter_area = $('#filter_area').val();
                        d.filter_counter = $('#filter_counter').val();
                        d.filter_box = $('#filter_box').val();
                    },
                },
                columns: [{
                        data: 'area'
                    },
                    {
                        data: 'counter'
                    },
                    {
                        data: 'box'
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
            $('#master_counter_id').val('').trigger('change');
            $('#master_box_id').val('').trigger('change');
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
                    } else if ($('#master_counter_id').val() == '') {
                        warningAlert('Please select Counter');
                    } else if ($('#master_box_id').val() == '') {
                        warningAlert('Please select Box');
                    } else if ($('#master_needle_id').val() == '') {
                        warningAlert('Please select Needle');
                    } else if ($('#qty').val() == '') {
                        warningAlert('Please insert Qty');
                    } else {
                        sendAjax('storeModal', {
                            url: "{{ route('user.stock.store') }}",
                            type: "POST",
                            data: {
                                master_area_id: $('#master_area_id').val(),
                                master_counter_id: $('#master_counter_id').val(),
                                master_box_id: $('#master_box_id').val(),
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
                    $('#edit_counter').val(response.counter.name);
                    $('#edit_box').val(response.box.name);
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

        function addStock(url) {
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
                    $('#add_counter').val(response.counter.name);
                    $('#add_box').val(response.box.name);
                    $('#add_brand').val(response.needle.brand);
                    $('#add_tipe').val(response.needle.tipe);
                    $('#add_size').val(response.needle.size);
                    $('#add_code').val(response.needle.code);
                    $('#add_machine').val(response.needle.machine);
                    $('#add_qty').val(0);
                    $('#addKey').val(response.id);

                    if ($.fn.DataTable.isDataTable("#tableHistory")) {
                        $('#tableHistory').html('');
                        tableHistory.destroy();
                    }

                    setTimeout(() => {
                        tableHistory = initDataTable('tableHistory', 'toolbarHistory', '', 0.4, {
                            ajax: {
                                url: "{{ route('user.stock.history') }}",
                                data: function(d) {
                                    d.stock_id = response.id;
                                }
                            },
                            columns: [{
                                    data: 'created_at'
                                },
                                {
                                    data: 'stock_before'
                                },
                                {
                                    data: 'qty'
                                },
                                {
                                    data: 'stock_after'
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
                url: "{{ route('user.stock.update') }}",
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

        function bersihkan(url) {
            customAlert({
                icon: 'question',
                title: 'Are you sure want to Clear this Box?',
                showCancelButton: true,
                confirmButtonText: 'Confirm Clear',
                confirmButtonColor: '#dc3545',
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
