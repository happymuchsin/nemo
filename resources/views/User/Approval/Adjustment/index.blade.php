@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Adjustment'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter-date />
                    <x-filter.filter :tipe="'select'" :label="'Status'" :id="'filter_status'" :colom="'col-sm-auto'">
                        <x-slot:option>
                            <option value="WAITING">WAITING</option>
                            <option value="APPROVE">APPROVE</option>
                            <option value="REJECT">REJECT</option>
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
                        <th rowspan="2">Requestor</th>
                        <th rowspan="2">Status</th>
                    </tr>
                    <tr>
                        <th>System</th>
                        <th>Actual</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'detail'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <input type="hidden" id="keyAdjustment">
            <input type="hidden" id="keyApproval">
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
            <x-modal.body :tipe="'textarea'" :label="'Remark'" :id="'remark'" :row="'3'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-success'" :id="'approve'" :onclick="'approval(\'' . 'APPROVE' . '\')'" :icon="'fa fa-check'" :name="'Approve'" />
            <x-layout.button :class="'btn-danger'" :id="'reject'" :onclick="'approval(\'' . 'REJECT' . '\')'" :icon="'fa fa-x'" :name="'Reject'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#bulan').val("{{ date('n') }}").trigger('change');
            $('#tahun').val("{{ date('Y') }}").trigger('change');
            $('#filter_status').val('WAITING').trigger('change');

            initSelect('approval', 'Select Approval', 'detailModal');

            $('#period').on('change', function() {
                setTable();
            })

            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('user.approval.adjustment.data') }}",
                    data: function(d) {
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();
                        d.filter_status = $('#filter_status').val();
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
                        data: 'requestor'
                    },
                    {
                        data: 'status'
                    },
                ],
            });
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
                    $('#keyAdjustment').val(response.id_adjustment);
                    $('#keyApproval').val(response.id_approval);

                    $('#approval').val(response.approval).trigger('change');
                    $('#period').val(response.period).trigger('change');
                    $('#remark').val(response.remark);

                    $('#approval').prop('disabled', true);
                    $('#period').prop('disabled', true);
                    $('#remark').prop('disabled', true);

                    if (response.status == 'APPROVE' || response.status == 'REJECT') {
                        $('#approve').prop('disabled', true);
                        $('#reject').prop('disabled', true);
                    } else {
                        $('#approve').prop('disabled', false);
                        $('#reject').prop('disabled', false);
                    }

                    $('#detailModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function setTable() {
            return new Promise((resolve, reject) => {
                try {
                    if ($.fn.DataTable.isDataTable(`#tableDetail`)) {
                        $(`#tableDetail`).html('');
                        $(`#tableDetail`).DataTable().clear().destroy();
                    }
                    setTimeout(() => {
                        tableItem = initDataTable('tableDetail', 'toolbarDetail', 'detailModal', 0.4, {
                            paging: false,
                            ordering: false,
                            searching: false,
                            ajax: {
                                url: "{{ route('user.adjustment.item', ['locale' => app()->getLocale()]) }}",
                                type: "POST",
                                data: function(d) {
                                    d.tipe = 'detail';
                                    d.id = $('#keyAdjustment').val();
                                    d.period = $('#period').val();
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

        function approval(status) {
            customAlert({
                icon: 'question',
                title: 'Are you sure want to ' + status + ' this Data?',
                showCancelButton: true,
                confirmButtonText: 'Confirm ' + status,
                confirmButtonColor: status == 'APPROVE' ? '#08b92c' : '#dc3545',
                callback: function() {
                    sendAjax('', {
                        url: "{{ route('user.approval.adjustment.approval') }}",
                        type: "POST",
                        data: {
                            id_adjustment: $('#keyAdjustment').val(),
                            id_approval: $('#keyApproval').val(),
                            status: status,
                        },
                        success: function(response) {
                            $('#detailModal').modal('toggle');
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

        function poto(data) {
            $('#potoModal').modal('show');
            $('#tempatpoto').html('<img width="100%" height="100%" src="' + data + '" />')
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
