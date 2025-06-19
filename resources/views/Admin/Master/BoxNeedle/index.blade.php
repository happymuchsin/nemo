@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Box Needle'">
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
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'store()'" :icon="'fa fa-save'" :name="'Save'" />
        </x-slot:footer>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            initSelect('master_area_id', 'Select Area', 'storeModal');
            initSelect('master_counter_id', 'Select Counter', 'storeModal');
            initSelect('master_box_id', 'Select Box', 'storeModal');
            initSelect('master_needle_id', 'Select Needle', 'storeModal');
            initSelect('needle_category', 'Select Category', 'storeModal');
            $('#master_area_id').on('change', function() {
                sendAjax('storeModal', {
                    url: "{{ route('admin.master.box-needle.spinner') }}",
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
                    url: "{{ route('admin.master.box-needle.spinner') }}",
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
                    url: "{{ route('admin.master.box-needle.spinner') }}",
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
                    url: "{{ route('admin.master.box-needle.data') }}",
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
                    } else {
                        sendAjax('storeModal', {
                            url: "{{ route('admin.master.box-needle.store') }}",
                            type: "POST",
                            data: {
                                master_area_id: $('#master_area_id').val(),
                                master_counter_id: $('#master_counter_id').val(),
                                master_box_id: $('#master_box_id').val(),
                                master_needle_id: $('#master_needle_id').val(),
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
