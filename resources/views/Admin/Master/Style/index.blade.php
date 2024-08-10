@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Style'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :tipe="'text'" :label="'Range Date'" :id="'filter_range_date'" />
                    <x-filter.filter :tipe="'select'" :label="'Buyer'" :id="'filter_master_buyer_id'">
                        <x-slot:option>
                            @foreach ($buyer as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'select'" :label="'Category'" :id="'filter_master_category_id'">
                        <x-slot:option>
                            @foreach ($category as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'select'" :label="'Sample'" :id="'filter_master_sample_id'">
                        <x-slot:option>
                            @foreach ($sample as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'select'" :label="'Fabric'" :id="'filter_master_fabric_id'">
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
                        <th>Buyer</th>
                        <th>Category</th>
                        <th>Sample</th>
                        <th>Fabric</th>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Buyer'" :id="'master_buyer_id'">
                <x-slot:option>
                    @foreach ($buyer as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Category'" :id="'master_category_id'">
                <x-slot:option>
                    @foreach ($category as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Sample'" :id="'master_sample_id'">
                <x-slot:option>
                    @foreach ($sample as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Fabric'" :id="'master_fabric_id'">
                <x-slot:option>
                    @foreach ($fabric as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" />
            <x-modal.body :tipe="'text'" :label="'Start - End'" :id="'range_date'" />
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

            $('#master_buyer_id').select2({
                placeholder: "Select Buyer",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            $('#master_category_id').select2({
                placeholder: "Select Category",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            $('#master_sample_id').select2({
                placeholder: "Select Sample",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });
            $('#master_fabric_id').select2({
                placeholder: "Select Fabric",
                dropdownParent: $('#crupModal'),
                width: '100%',
            });

            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * 0.6,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('admin.master.style.data') }}",
                    data: function(d) {
                        d.filter_range_date = $('#filter_range_date').val();
                        d.filter_master_buyer_id = $('#filter_master_buyer_id').val();
                        d.filter_master_category_id = $('#filter_master_category_id').val();
                        d.filter_master_sample_id = $('#filter_master_sample_id').val();
                        d.filter_master_fabric_id = $('#filter_master_fabric_id').val();
                    }
                },
                columns: [{
                        data: 'buyer'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'sample'
                    },
                    {
                        data: 'fabric'
                    },
                    {
                        data: 'name'
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
                pageLength: 50,
                lengthMenu: [
                    [50, 100, 500, -1],
                    [50, 100, 500, "All"]
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
            $('#master_buyer_id').val('').trigger('change');
            $('#master_category_id').val('').trigger('change');
            $('#master_sample_id').val('').trigger('change');
            $('#master_fabric_id').val('').trigger('change');
            $('#name').val('');
            $('#range_date').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            if ($('#master_buyer_id').val() == '') {
                Swal.fire('Warning!', 'Please select Buyer', 'warning');
            } else if ($('#master_category_id').val() == '') {
                Swal.fire('Warning!', 'Please select Category', 'warning');
            } else if ($('#master_sample_id').val() == '') {
                Swal.fire('Warning!', 'Please select Sample', 'warning');
            } else if ($('#master_fabric_id').val() == '') {
                Swal.fire('Warning!', 'Please select Fabric', 'warning');
            } else if ($('#name').val() == '') {
                Swal.fire('Warning!', 'Please insert Name', 'warning');
            } else if ($('#range_date').val() == '') {
                Swal.fire('Warning!', 'Please select Start - End', 'warning');
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.master.style.crup') }}",
                    data: {
                        'id': $('#key').val(),
                        'name': $('#name').val(),
                        'range_date': $('#range_date').val(),
                        'master_buyer_id': $('#master_buyer_id').val(),
                        'master_category_id': $('#master_category_id').val(),
                        'master_sample_id': $('#master_sample_id').val(),
                        'master_fabric_id': $('#master_fabric_id').val(),
                    },
                    beforeSend: function() {
                        Swal.fire({
                            iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                            title: 'Please Wait',
                            html: 'Fetching your data..',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                        Swal.showLoading();
                    },
                    complete: function() {
                        // Swal.close();
                    },
                    success: function(response) {
                        $('#crupModal').modal('toggle');
                        Swal.fire('Success!', response, 'success');
                        setTimeout(() => {
                            Swal.close();
                        }, 1000);
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Warning!', response.responseText, 'warning');
                    }
                })
            }
        };

        function edit(url) {
            $.ajax({
                    type: "get",
                    url: url,
                    beforeSend: function() {
                        Swal.fire({
                            iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                            title: 'Please Wait',
                            html: 'Fetching your data..',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                        Swal.showLoading();
                    },
                    complete: function() {
                        Swal.close();
                    },
                })
                .done(function(response) {
                    $('#crupJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#master_buyer_id').val(response.master_buyer_id).trigger('change');
                    $('#master_category_id').val(response.master_category_id).trigger('change');
                    $('#master_sample_id').val(response.master_sample_id).trigger('change');
                    $('#master_fabric_id').val(response.master_fabric_id).trigger('change');
                    $('#name').val(response.name);
                    $('#range_date').val(response.range_date);
                    $('#key').val(response.id);
                    $('#crupModal').modal('toggle');
                });
        }

        function hapus(url) {
            Swal.fire({
                icon: 'question',
                title: 'Are you sure want to permanent Delete this Data?',
                showCancelButton: true,
                confirmButtonText: 'Confirm Delete',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: url,
                        type: "GET",
                        beforeSend: function() {
                            Swal.fire({
                                iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                                title: 'Please Wait',
                                html: 'Fetching your data..',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            });
                            Swal.showLoading();
                        },
                        complete: function() {
                            // Swal.close();
                        },
                        success: function(response) {
                            Swal.fire('Success!', response, 'success');
                            setTimeout(() => {
                                Swal.close();
                            }, 1000);
                            table.ajax.reload();
                        },
                        error: function(response) {
                            Swal.fire('Warning!', response.responseText, 'warning');
                        }
                    });
                }
            })
        }
    </script>
@endsection
