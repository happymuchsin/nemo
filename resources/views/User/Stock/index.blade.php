@extends('layouts.user', ['page' => $page, 'sidebar' => true])
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
            {{-- <x-modal.body :tipe="'select'" :label="'Needle'" :id="'master_needle_id'">
                <x-slot:option>
                    @foreach ($needle as $d)
                        <option value="{{ $d->id }}">{{ "$d->brand - $d->tipe - $d->size - $d->code - $d->machine" }}
                        </option>
                    @endforeach
                </x-slot:option>
            </x-modal.body> --}}
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
            $('#master_area_id').select2({
                placeholder: "Select Area",
                dropdownParent: $('#storeModal'),
                width: '100%',
            });
            $('#master_area_id').on('change', function() {
                $.ajax({
                    url: "{{ route('user.stock.spinner') }}",
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'counter',
                        master_area_id: $(this).val(),
                    },
                    success: function(data) {
                        $('#master_counter_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_counter_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    }
                })
            });
            $('#master_counter_id').select2({
                placeholder: "Select Counter",
                dropdownParent: $('#storeModal'),
                width: '100%',
            });
            $('#master_counter_id').on('change', function() {
                $.ajax({
                    url: "{{ route('user.stock.spinner') }}",
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'box',
                        master_area_id: $('#master_area_id').val(),
                        master_counter_id: $(this).val(),
                    },
                    success: function(data) {
                        $('#master_box_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_box_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    }
                })
            });
            $('#master_box_id').select2({
                placeholder: "Select Box",
                dropdownParent: $('#storeModal'),
                width: '100%',
            });
            $('#needle_category').select2({
                placeholder: "Select Needle Category",
                dropdownParent: $('#storeModal'),
                width: '100%',
            });
            $('#needle_category').on('change', function() {
                $.ajax({
                    url: "{{ route('user.stock.spinner') }}",
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'needle',
                        needle_category: $(this).val(),
                    },
                    success: function(data) {
                        $('#master_needle_id').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#master_needle_id').append('<option value="' + v.id +
                                '">' + v.name + '</option>')
                        })
                    }
                })
            })
            $('#master_needle_id').select2({
                placeholder: "Select Needle",
                dropdownParent: $('#storeModal'),
                width: '100%',
            });
            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * 0.6,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('user.stock.data') }}",
                    data: function(d) {
                        d.filter_area = $('#filter_area').val();
                        d.filter_counter = $('#filter_counter').val();
                        d.filter_box = $('#filter_box').val();
                    }
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
                order: [],
                pageLength: 50,
                lengthMenu: [
                    [50, 100, 500, -1],
                    [50, 100, 500, "All"]
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

            // collectMasterNeedleId = [];

            // if ($.fn.DataTable.isDataTable("#tableStore")) {
            //     $('#tableStore').html('');
            //     $('#tableStore').DataTable().clear().destroy();
            // }

            // setTimeout(() => {
            //     tableStore = $('#tableStore').DataTable({
            //         dom: '<"toolbarStore">flrtip',
            //         scrollY: screen.height * 0.6,
            //         scrollX: true,
            //         scrollCollapse: true,
            //         paging: false,
            //         searching: false,
            //         ordering: false,
            //     });
            // }, 250);

            $('#master_area_id').val('').trigger('change');
            $('#master_counter_id').val('').trigger('change');
            $('#master_box_id').val('').trigger('change');
            $('#master_needle_id').val('').trigger('change');
            $('#needle_category').val('all').trigger('change');
            $('#qty').val('');
            $('#storeModal').modal('toggle');
        }

        // function addToTable() {
        //     if ($('#master_needle_id').val() == '') {
        //         Swal.fire('Warning!', 'Please select Item', 'warning');
        //     } else {
        //         if (collectMasterNeedleId.includes($('#master_needle_id').val())) {
        //             Swal.fire('Warning!', 'Already Submit', 'warning');
        //         } else {
        //             $.ajaxSetup({
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 }
        //             });
        //             $.ajax({
        //                 type: "POST",
        //                 url: "{{ route('user.stock.needle') }}",
        //                 data: {
        //                     'master_needle_id': $('#master_needle_id').val(),
        //                 },
        //                 beforeSend: function() {
        //                     Swal.fire({
        //                         iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
        //                         title: 'Please Wait',
        //                         html: 'Fetching your data..',
        //                         allowOutsideClick: false,
        //                         allowEscapeKey: false,
        //                     });
        //                     Swal.showLoading();
        //                 },
        //                 complete: function() {
        //                     // Swal.close();
        //                 },
        //                 success: function(response) {
        //                     Swal.close();
        //                     collectMasterNeedleId.push($('#master_needle_id').val());
        //                     tableStore.row.add([
        //                         response.brand,
        //                         response.tipe,
        //                         response.size,
        //                         response.qty,
        //                         response.action,
        //                     ]).draw(false);
        //                 },
        //                 error: function(response) {
        //                     Swal.fire('Warning!', response.responseText, 'warning');
        //                 }
        //             })
        //         }
        //     }
        // }

        // function hapusStore(tdElement, master_needle_id) {
        //     Swal.fire({
        //         icon: 'question',
        //         title: 'Are you sure want to Delete this Item?',
        //         showCancelButton: true,
        //         confirmButtonText: 'Confirm Delete',
        //         confirmButtonColor: '#dc3545'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             tableStore.row($(tdElement).parents('tr')).remove().draw();
        //             const index = collectMasterNeedleId.indexOf(master_needle_id);
        //             collectMasterNeedleId.splice(index, 1);
        //         }
        //     })
        // }

        // function store() {
        //     if (collectMasterNeedleId.length == 0) {
        //         Swal.fire('Warning!', 'Please insert Needle', 'warning');
        //     } else {
        //         Swal.fire({
        //             icon: 'question',
        //             title: 'Is the data correct?',
        //             showCancelButton: true,
        //             confirmButtonText: 'Yes',
        //             confirmButtonColor: '#08fe3e'
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 var master_needle_id = $("input[name='master_needle_id[]']").map(function() {
        //                     return $(this).val();
        //                 }).get();
        //                 var qty = $("input[name='qty[]']").map(function() {
        //                     return $(this).val();
        //                 }).get();

        //                 if ($('#tanggal').val() == '') {
        //                     Swal.fire('Warning!', 'Please select Date', 'warning');
        //                 } else if ($('#master_area_id').val() == '') {
        //                     Swal.fire('Warning!', 'Please select Area', 'warning');
        //                 } else if ($('#master_counter_id').val() == '') {
        //                     Swal.fire('Warning!', 'Please select Counter', 'warning');
        //                 } else if ($('#master_box_id').val() == '') {
        //                     Swal.fire('Warning!', 'Please select Box', 'warning');
        //                 } else if ($.inArray('', qty) !== -1) {
        //                     Swal.fire('Warning!', 'Please insert Qty', 'warning');
        //                 } else {
        //                     $.ajaxSetup({
        //                         headers: {
        //                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                         }
        //                     });
        //                     $.ajax({
        //                         type: "POST",
        //                         url: "{{ route('user.stock.store') }}",
        //                         data: {
        //                             master_needle_id: master_needle_id,
        //                             qty: qty,
        //                             tanggal: $('#tanggal').val(),
        //                             master_area_id: $('#master_area_id').val(),
        //                             master_counter_id: $('#master_counter_id').val(),
        //                             master_box_id: $('#master_box_id').val(),
        //                         },
        //                         beforeSend: function() {
        //                             Swal.fire({
        //                                 iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
        //                                 title: 'Please Wait',
        //                                 html: 'Fetching your data..',
        //                                 allowOutsideClick: false,
        //                                 allowEscapeKey: false,
        //                             });
        //                             Swal.showLoading();
        //                         },
        //                         complete: function() {
        //                             // Swal.close();
        //                         },
        //                         success: function(response) {
        //                             $('#storeModal').modal('toggle');
        //                             Swal.fire('Success!', response, 'success');
        //                             setTimeout(() => {
        //                                 Swal.close();
        //                             }, 1000);
        //                             table.ajax.reload();
        //                         },
        //                         error: function(response) {
        //                             Swal.fire('Warning!', response.responseText, 'warning');
        //                         }
        //                     })
        //                 }
        //             }
        //         })
        //     }
        // }

        function store() {
            Swal.fire({
                icon: 'question',
                title: 'Is the data correct?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#08fe3e'
            }).then((result) => {
                if (result.isConfirmed) {
                    if ($('#master_area_id').val() == '') {
                        Swal.fire('Warning!', 'Please select Area', 'warning');
                    } else if ($('#master_counter_id').val() == '') {
                        Swal.fire('Warning!', 'Please select Counter', 'warning');
                    } else if ($('#master_box_id').val() == '') {
                        Swal.fire('Warning!', 'Please select Box', 'warning');
                    } else if ($('#master_needle_id').val() == '') {
                        Swal.fire('Warning!', 'Please select Needle', 'warning');
                    } else if ($('#qty').val() == '') {
                        Swal.fire('Warning!', 'Please insert Qty', 'warning');
                    } else {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: "{{ route('user.stock.store') }}",
                            data: {
                                master_area_id: $('#master_area_id').val(),
                                master_counter_id: $('#master_counter_id').val(),
                                master_box_id: $('#master_box_id').val(),
                                master_needle_id: $('#master_needle_id').val(),
                                qty: $('#qty').val(),
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
                                $('#storeModal').modal('toggle');
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
                }
            })
        }

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
                });
        }

        function addStock(url) {
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
                        tableHistory = $('#tableHistory').DataTable({
                            dom: '<"toolbar">flrtip',
                            scrollY: screen.height * 0.6,
                            scrollX: true,
                            scrollCollapse: true,
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
                            order: [],
                            paging: false
                        });
                    }, 250);
                    $('#addModal').modal('toggle');
                });
        }

        function update(tipe) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('user.stock.update') }}",
                data: {
                    id: tipe == 'edit' ? $('#editKey').val() : $('#addKey').val(),
                    tipe: tipe,
                    in: tipe == 'edit' ? $('#edit_qty').val() : $('#add_qty').val(),
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
                    if (tipe == 'edit')
                        $('#updateModal').modal('toggle');
                    else
                        $('#addModal').modal('toggle');
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

        function bersihkan(url) {
            Swal.fire({
                icon: 'question',
                title: 'Are you sure want to Clear this Box?',
                showCancelButton: true,
                confirmButtonText: 'Confirm Clear',
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

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
