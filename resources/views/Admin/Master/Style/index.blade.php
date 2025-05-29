@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Style'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'text'" :label="'Range Date'" :id="'filter_range_date'" />
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'select'" :label="'Buyer'" :id="'filter_master_buyer_id'">
                        <x-slot:option>
                            @foreach ($buyer as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'select'" :label="'Category'" :id="'filter_master_category_id'">
                        <x-slot:option>
                            @foreach ($category as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'select'" :label="'Sub Category'" :id="'filter_master_sub_category_id'">
                        <x-slot:option>
                            @foreach ($subcategory as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'select'" :label="'Sample'" :id="'filter_master_sample_id'">
                        <x-slot:option>
                            @foreach ($sample as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'select'" :label="'Fabric'" :id="'filter_master_fabric_id'">
                        <x-slot:option>
                            @foreach ($fabric as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'"
                            :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>SRF No</th>
                        <th>Buyer</th>
                        <th>Style</th>
                        <th>Sample Type</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Fabric</th>
                        <th>Season</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'" :ukuran="'modal-lg'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <div class="row">
                <div class="col-sm-6">
                    <label>SRF No</label>
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="number" min="0" max="999" class="form-control no-spin" id="depan"
                                autocomplete="off">
                        </div>
                        <div class="col-sm-4">
                            <select id="tengah">
                                <option value=""></option>
                                @for ($mm = 1; $mm <= 12; $mm++)
                                    <option value="{{ strtoupper(date('M', mktime(0, 0, 0, $mm, 1))) }}">
                                        {{ strtoupper(date('M', mktime(0, 0, 0, $mm, 1))) }}</option>;
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select id="belakang">
                                <option value=""></option>
                                @for ($x = date('y') + 1; $x >= 23; $x--)
                                    <option value="{{ $x }}">{{ $x }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'select'" :label="'Buyer'" :id="'master_buyer_id'">
                        <x-slot:option>
                            @foreach ($buyer as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'text'" :label="'Style'" :id="'name'" />
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'select'" :label="'Sample Type'" :id="'master_sample_id'">
                        <x-slot:option>
                            @foreach ($sample as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'select'" :label="'Category'" :id="'master_category_id'">
                        <x-slot:option>
                            @foreach ($category as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'select'" :label="'Sub Category'" :id="'master_sub_category_id'">
                        <x-slot:option>
                            @foreach ($subcategory as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'select'" :label="'Fabric'" :id="'master_fabric_id'">
                        <x-slot:option>
                            @foreach ($fabric as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-modal.body>
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'text'" :label="'Season'" :id="'season'" />
                </div>
                <div class="col-sm-6">
                    <x-modal.body :tipe="'text'" :label="'Start - End'" :id="'range_date'" />
                </div>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'imp'">
        <x-slot:body>
            <x-modal.body :tipe="'file'" :label="'File Format Excel'" :id="'excel'" :accept="'.xlsx'" />
        </x-slot:body>
        <x-slot:footer>
            <a class="btn btn-sm btn-warning" href="{{ route('admin.master.style.template') }}"><i
                    class="fa fa-file-download"></i> Template</a>
            <x-layout.button :class="'btn-primary'" :id="''" :onclick="'Import()'" :icon="'fa fa-upload'"
                :name="'Import'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            $('#filter_range_date').val("{{ date('Y-m-d', strtotime('-1 month')) . ' - ' . date('Y-m-d') }}")

            $("#filter_range_date").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#filter_range_date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            $("#range_date").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#range_date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            $('#depan').on('input', function() {
                var value = parseInt($(this).val());
                if (isNaN(value)) {
                    $(this).val(0);
                } else {
                    if (value < 0) {
                        $(this).val(0);
                    } else if (value > 999) {
                        $(this).val(999);
                    }
                }
            });
            initSelect('tengah', 'Select Month', 'crupModal');
            initSelect('belakang', 'Select Year', 'crupModal');
            initSelect('master_buyer_id', 'Select Buyer', 'crupModal');
            initSelect('master_category_id', 'Select Category', 'crupModal');
            initSelect('master_sub_category_id', 'Select Sub Category', 'crupModal');
            initSelect('master_sample_id', 'Select Sample', 'crupModal');
            initSelect('master_fabric_id', 'Select Fabric', 'crupModal');

            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.style.data') }}",
                    data: function(d) {
                        d.filter_range_date = $('#filter_range_date').val();
                        d.filter_master_buyer_id = $('#filter_master_buyer_id').val();
                        d.filter_master_category_id = $('#filter_master_category_id').val();
                        d.filter_master_sub_category_id = $('#filter_master_sub_category_id').val();
                        d.filter_master_sample_id = $('#filter_master_sample_id').val();
                        d.filter_master_fabric_id = $('#filter_master_fabric_id').val();
                    }
                },
                columns: [{
                        data: 'srf'
                    },
                    {
                        data: 'buyer'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'sample'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'sub_category'
                    },
                    {
                        data: 'fabric'
                    },
                    {
                        data: 'season'
                    },
                    {
                        data: 'start'
                    },
                    {
                        data: 'end'
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
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fa fa-circle-plus" /></i> New</button> <button class="btn btn-sm btn-success" onclick="btnImport();"><i class="fa fa-file-excel" /></i> Import</button>'
            );
        })

        function btnImport() {
            $('#impJudul').html('<h5 class="modal-title"><i class="fa fa-file-excel"></i> Import</h5>');
            $('#impHeader').addClass('bg-success');
            $('#impHeader').removeClass('bg-info');
            $('#excel').val('');
            $('#excel').text('');
            $('#impModal').modal('toggle');
        }

        function Import() {
            var data = new FormData();
            data.append('excel', $('#excel')[0].files[0]);
            sendAjax('impModal', {
                url: "{{ route('admin.master.style.import') }}",
                type: "POST",
                data: data,
                success: function(response) {
                    $('#impModal').modal('toggle');
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

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-plus"></i> Input</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#master_buyer_id').val('').trigger('change');
            $('#master_category_id').val('').trigger('change');
            $('#master_sub_category_id').val('').trigger('change');
            $('#master_sample_id').val('').trigger('change');
            $('#master_fabric_id').val('').trigger('change');
            $('#name').val('');
            $('#depan').val('');
            $('#tengah').val("{{ strtoupper(date('M')) }}").trigger('change');
            $('#belakang').val("{{ date('y') }}").trigger('change');
            $('#season').val('');
            $('#range_date').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#depan').val() == '' || $('#tengah').val() == '' || $('#belakang').val() == '') {
                warningAlert('Please insert SRF No');
            } else if ($('#master_buyer_id').val() == '') {
                warningAlert('Please select Buyer');
            } else if ($('#master_category_id').val() == '') {
                warningAlert('Please select Category');
            } else if ($('#master_sub_category_id').val() == '') {
                warningAlert('Please select Sub Category');
            } else if ($('#master_sample_id').val() == '') {
                warningAlert('Please select Sample');
            } else if ($('#master_fabric_id').val() == '') {
                warningAlert('Please select Fabric');
            } else if ($('#name').val() == '') {
                warningAlert('Please insert Name');
            } else if ($('#season').val() == '') {
                warningAlert('Please insert Season');
            } else if ($('#range_date').val() == '') {
                warningAlert('Please select Start - End');
            } else {
                var depan = $('#depan').val();
                if (depan.length == 1) {
                    depan = '00' + depan;
                } else if (depan.length == 2) {
                    depan = '0' + depan;
                } else {
                    depan = depan;
                }
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.style.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'name': $('#name').val(),
                        'srf': depan + $('#tengah').val() + $('#belakang').val(),
                        'season': $('#season').val(),
                        'range_date': $('#range_date').val(),
                        'master_buyer_id': $('#master_buyer_id').val(),
                        'master_category_id': $('#master_category_id').val(),
                        'master_sub_category_id': $('#master_sub_category_id').val(),
                        'master_sample_id': $('#master_sample_id').val(),
                        'master_fabric_id': $('#master_fabric_id').val(),
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
                    $('#master_buyer_id').val(response.master_buyer_id).trigger('change');
                    $('#master_category_id').val(response.master_category_id).trigger('change');
                    $('#master_sub_category_id').val(response.master_sub_category_id).trigger('change');
                    $('#master_sample_id').val(response.master_sample_id).trigger('change');
                    $('#master_fabric_id').val(response.master_fabric_id).trigger('change');
                    $('#name').val(response.name);
                    $('#srf').val(response.srf);
                    $('#season').val(response.season);
                    $('#range_date').val(response.range_date);
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
