@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Placement'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>NIK / Username</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Counter Member</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'select'" :label="'Type'" :id="'reff'">
                <x-slot:option>
                    <option value="line">Line</option>
                    <option value="counter">Admin Counter</option>
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Location'" :id="'lokasi'" />
            <div id="divKounter">
                <x-modal.body :tipe="'select'" :label="'Counter Member'" :id="'kounter'" />
            </div>
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
            initSelect('reff', 'Select Type', 'crupModal');
            initSelect('lokasi', 'Select Location', 'crupModal');
            initSelect('kounter', 'Select Counter', 'crupModal');
            $('#reff').on('change', function() {
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.placement.spinner') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tipe: 'reff',
                        reff: $(this).val(),
                    },
                    success: function(data) {
                        unwaitAlert();
                        if ($('#reff').val() == 'line') {
                            $('#divKounter').prop('hidden', false);
                        } else {
                            $('#divKounter').prop('hidden', true);
                        }
                        $('#lokasi').html('<option value=""></option>');
                        $.each(data, function(k, v) {
                            $('#lokasi').append('<option value="' + v.id + '">' + v.area
                                .name + ' - ' + v.name + '</option>')
                        })
                        $('#kounter').html('');
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            });
            $('#lokasi').on('change', function() {
                if ($('#reff').val() == 'line') {
                    sendAjax('crupModal', {
                        url: "{{ route('admin.master.placement.spinner') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            tipe: 'lokasi',
                            reff: $('#reff').val(),
                            lokasi: $(this).val(),
                        },
                        success: function(data) {
                            unwaitAlert();
                            $('#kounter').html('<option value=""></option>');
                            $.each(data, function(k, v) {
                                $('#kounter').append('<option value="' + v.id + '">' + v
                                    .area.name + ' - ' + v.name + '</option>')
                            })
                        },
                        error: function(response) {
                            warningAlert(response.responseText);
                        }
                    })
                }
            });
            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('admin.master.placement.data') }}",
                },
                columns: [{
                        data: 'username'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'division'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'tipe'
                    },
                    {
                        data: 'lokasi'
                    },
                    {
                        data: 'counter'
                    },
                    {
                        data: 'action'
                    },
                ],
                paging: false,
            });
        })

        function crup() {
            if ($('#lokasi').val() == '') {
                warningAlert('Please select Location');
            } else {
                if ($('#lokasi').val() == 'line' && $('#kounter').val() == '') {
                    warningAlert('Please select Counter');
                }
                sendAjax('crupModal', {
                    url: "{{ route('admin.master.placement.crup') }}",
                    type: "POST",
                    data: {
                        'id': $('#key').val(),
                        'reff': $('#reff').val(),
                        'lokasi': $('#lokasi').val(),
                        'counter': $('#kounter').val(),
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
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Placement ' + response
                        .username +
                        ' - ' + response.name + '</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#reff').val(response.reff).trigger('change');
                    setTimeout(() => {
                        $('#lokasi').val(response.lokasi).trigger('change');
                    }, 500);
                    setTimeout(() => {
                        $('#kounter').val(response.counter).trigger('change');
                    }, 1000);
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
