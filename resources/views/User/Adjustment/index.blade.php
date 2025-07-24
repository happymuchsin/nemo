@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Adjustment'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :label="'Year'" :id="'tahun'" :tipe="'select'" :colom="'col-sm-auto'" :all-option="false">
                        <x-slot:option>
                            @for ($x = date('Y'); $x >= 2024; $x--)
                                <option value="{{ $x }}">{{ $x }}</option>
                            @endfor
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
                        <th rowspan="2">Period</th>
                        <th colspan="2">Qty</th>
                        <th rowspan="2">Remark</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>System</th>
                        <th>Actual</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="key">
            <div class="row">
                <div class="col-sm-2">
                    <x-modal.body :tipe="'select'" :label="'Approval'" :id="'approval'">
                        <x-slot:option>
                            @foreach ($master_approval as $ma)
                                <option value="{{ $ma->id }}">{{ $ma->user->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-2">
                    <x-modal.body :tipe="'month'" :label="'Period'" :id="'period'" />
                </div>
            </div>
            <x-layout.table :id="'tableItem'">
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
                        <th>System</th>
                        <th>Actual</th>
                        <th>Balance</th>
                        <th>Remark</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
            <x-modal.body :tipe="'textarea'" :label="'Remark'" :id="'remark'" :row="'3'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Save'" />
            <x-layout.button :class="'btn-warning'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'" :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'detail'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="detail_key">
            <div class="row">
                <div class="col-sm-2">
                    <x-modal.body :tipe="'select'" :label="'Approval'" :id="'detail_approval'">
                        <x-slot:option>
                            @foreach ($master_approval as $ma)
                                <option value="{{ $ma->id }}">{{ $ma->user->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-2">
                    <x-modal.body :tipe="'month'" :label="'Period'" :id="'detail_period'" />
                </div>
            </div>
            <x-layout.table :id="'tableDetail'">
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
                        <th>System</th>
                        <th>Actual</th>
                        <th>Balance</th>
                        <th>Remark</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
            <x-modal.body :tipe="'textarea'" :label="'Remark'" :id="'detail_remark'" :row="'3'" />
        </x-slot:body>
    </x-modal.modal>

    <script>
        var table = null,
            tableItem = null,
            tableDetail = null,
            mode = null;
        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);

            $('#tahun').val("{{ date('Y') }}").trigger('change');

            $('#period, #detail_period').on('change', function() {
                setTable();
            })

            initSelect('approval', 'Select Approval', 'crupModal');
            initSelect('detail_approval', 'Select Approval', 'detailModal');

            setTimeout(() => {
                table = initDataTable('table', '', '', '', {
                    ajax: {
                        url: "{{ route('user.adjustment.data') }}",
                        data: function(d) {
                            d.tahun = $('#tahun').val();
                        },
                    },
                    columns: [{
                            data: 'period'
                        },
                        {
                            data: 'before'
                        },
                        {
                            data: 'after'
                        },
                        {
                            data: 'remark'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'action'
                        },
                    ],
                    paging: false,
                });
                $('div.toolbar').html(
                    '<button class="btn btn-sm btn-success" onclick="add();"><i class="fal fa-circle-plus" /></i> New</button>'
                );
            }, 250);
        })

        function detail(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#detailJudul').html('<h5 class="modal-title"><i class="fa fa-file-info"></i> Detail</h5>');
                    $('#detailHeader').removeClass('bg-success');
                    $('#detailHeader').addClass('bg-info');
                    $('#detail_key').val(response.id);

                    mode = 'detail';
                    $('#detail_approval').val(response.approval).trigger('change');
                    $('#detail_period').val(response.period).trigger('change');
                    $('#detail_remark').val(response.remark);

                    $('#detail_approval').prop('disabled', true);
                    $('#detail_period').prop('disabled', true);
                    $('#detail_remark').prop('disabled', true);

                    $('#detailModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fal fa-file-plus"></i> Input</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#key').val(0);

            mode = 'add';
            $('#approval').val('').trigger('change');
            $('#period').val('').trigger('change');

            $('#crupModal').modal('toggle');
        }

        function setTable() {
            return new Promise((resolve, reject) => {
                try {
                    var modal = '',
                        divTable = '',
                        divToolbar = '',
                        period = '',
                        key = '';
                    if (mode == 'add' || mode == 'edit') {
                        modal = 'crupModal';
                        divTable = 'tableItem';
                        divToolbar = 'toolbarItem';
                        key = $('#key').val();
                        period = $('#period').val();
                    } else if (mode == 'detail') {
                        modal = 'detailModal';
                        divTable = 'tableDetail';
                        divToolbar = 'toolbarDetail';
                        key = $('#detail_key').val();
                        period = $('#detail_period').val();
                    }
                    if ($.fn.DataTable.isDataTable(`#${divTable}`)) {
                        $(`#${divTable}`).html('');
                        $(`#${divTable}`).DataTable().clear().destroy();
                    }
                    setTimeout(() => {
                        tableItem = initDataTable(divTable, divToolbar, modal, 0.4, {
                            paging: false,
                            ordering: false,
                            searching: false,
                            ajax: {
                                url: "{{ route('user.adjustment.item', ['locale' => app()->getLocale()]) }}",
                                type: "POST",
                                data: function(d) {
                                    d.tipe = mode;
                                    d.id = key;
                                    d.period = period;
                                },
                            },
                            drawCallback: function() {
                                $('.input-actual').each(function() {
                                    var id = $(this).attr('id');
                                    var parent_id = id.replace('actual', '');
                                    $('#' + id).on('input', function() {
                                        $('#balance' + parent_id).val($('#system' + parent_id).val() - $(this).val());
                                    });
                                })
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
                                    data: 'type'
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
                                    data: 'system'
                                },
                                {
                                    data: 'actual'
                                },
                                {
                                    data: 'balance'
                                },
                                {
                                    data: 'remark'
                                },
                            ],
                            initComplete: function() {
                                resolve(true);
                            },
                        });
                    }, 500);
                } catch (e) {
                    reject(e);
                }
            });
        }

        function crup() {
            if ($('#approval').val() == '') {
                warningAlert('Please select Approval');
            } else if ($('#period').val() == '') {
                warningAlert('Please select Period');
            } else {
                customAlert({
                    icon: 'question',
                    title: 'Is the data correct?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#08fe3e',
                    callback: function() {
                        var detailAdj = [];

                        $('[id^="stockX"]').each(function() {
                            var stock_id = $(this).attr('id').replace('stockX', '');
                            var before = $(`#system${stock_id}`).val();
                            var after = $(`#actual${stock_id}`).val();
                            var remark = $(`#remark${stock_id}`).val();

                            if (after == '') {
                                after = before;
                            }

                            // Push ke array
                            detailAdj.push({
                                stock_id: stock_id,
                                before: before,
                                after: after,
                                remark: remark,
                            });
                        });

                        sendAjax('storeModal', {
                            url: "{{ route('user.adjustment.crup') }}",
                            type: "POST",
                            data: {
                                id: $('#key').val(),
                                approval: $('#approval').val(),
                                period: $('#period').val(),
                                remark: $('#remark').val(),
                                detailAdj: detailAdj,
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
                    $('#key').val(response.id);

                    mode = 'edit';
                    $('#approval').val(response.approval).trigger('change');
                    $('#period').val(response.period).trigger('change');
                    $('#remark').val(response.remark);

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

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
