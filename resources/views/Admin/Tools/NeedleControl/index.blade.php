@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Needle Control'">
        <x-slot:body>
            <h5>Time Limit</h5>
            <x-layout.table :id="'tableLimit'">
                <x-slot:thead>
                    <tr>
                        <th>Limit (in Seconds)</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
            <hr>
            <h5>Needle</h5>
            <br>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'date'" :label="'Date'" :id="'filter_date'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'setTable()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'tableControl'">
                <x-slot:thead>
                    <tr>
                        <th>Time</th>
                        <th>Scan RFID Operator</th>
                        <th>Scan Box Needle</th>
                        <th>Line</th>
                        <th>User</th>
                        <th>Buyer</th>
                        <th>Style</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Remark</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'update'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'number'" :label="'Time in Seconds'" :id="'waktu'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'update()'" :icon="'fa fa-save'" :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'poto'">
        <x-slot:body>
            <div class="form-group" id="tempatpoto"></div>
        </x-slot:body>
    </x-modal.modal>

    <script>
        var table = null;
        $(document).ready(function() {
            $('#filter_date').val("{{ date('Y-m-d') }}").trigger('change');

            setTable();
        })

        function setTable() {
            return new Promise((resolve, reject) => {
                try {
                    waitAlert();
                    const promiseLimit = new Promise((res, rej) => {
                        if ($.fn.DataTable.isDataTable("#tableLimit")) {
                            $('#tableLimit').html('');
                            $('#tableLimit').DataTable().clear().destroy();
                        }
                        setTimeout(() => {
                            tableLimit = initDataTable('tableLimit', 'toolbarLimit', '', '', {
                                ajax: {
                                    url: "{{ route('admin.tools.needle-control.data') }}",
                                    data: function(d) {
                                        d.tipe = 'limit';
                                    },
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                },
                                columns: [{
                                        data: 'waktu'
                                    },
                                    {
                                        data: 'action'
                                    },
                                ],
                                paging: false,
                                info: false,
                                searching: false,
                                initComplete: function() {
                                    res(true);
                                },
                            });
                        }, 250);
                    });

                    const promiseControl = new Promise((res, rej) => {
                        if ($.fn.DataTable.isDataTable("#tableControl")) {
                            $('#tableControl').html('');
                            $('#tableControl').DataTable().clear().destroy();
                        }
                        setTimeout(() => {
                            tableControl = initDataTable('tableControl', 'toolbarControl', '', '', {
                                ajax: {
                                    url: "{{ route('admin.tools.needle-control.data') }}",
                                    data: function(d) {
                                        d.tipe = 'needle';
                                        d.filter_date = $('#filter_date').val();
                                    },
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                },
                                columns: [{
                                        data: 'time'
                                    },
                                    {
                                        data: 'rfid'
                                    },
                                    {
                                        data: 'box'
                                    },
                                    {
                                        data: 'line'
                                    },
                                    {
                                        data: 'user'
                                    },
                                    {
                                        data: 'buyer'
                                    },
                                    {
                                        data: 'style'
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
                                        data: 'remark'
                                    },
                                    {
                                        data: 'gambar'
                                    },
                                    {
                                        data: 'action'
                                    },
                                ],
                                paging: false,
                                initComplete: function() {
                                    res(true);
                                },
                            });
                        }, 250);
                    });

                    // Menunggu kedua promise selesai
                    Promise.all([promiseLimit, promiseControl])
                        .then((results) => {
                            resolve({
                                message: true,
                            });
                            unwaitAlert();
                        })
                        .catch((error) => {
                            unwaitAlert();
                            reject(error);
                        });
                } catch (e) {
                    unwaitAlert();
                    reject(e);
                }
            });
        }

        function poto(data) {
            $('#potoModal').modal('show');
            $('#tempatpoto').html('<img width="100%" height="100%" src="' + data + '" />')
        }

        function update() {
            if ($('#waktu').val() == '') {
                warningAlert('Please insert Time in Seconds');
            } else {
                customAlert({
                    icon: 'question',
                    title: 'Is the data correct?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#08fe3e',
                    callback: function() {
                        sendAjax('updateModal', {
                            url: "{{ route('admin.tools.needle-control.update') }}",
                            type: "POST",
                            data: {
                                id: $('#key').val(),
                                tipe: 'limit',
                                waktu: $('#waktu').val(),
                            },
                            success: function(response) {
                                $('#updateModal').modal('toggle');
                                successAlert(response);
                                closeAlert();
                                setTimeout(() => {
                                    setTable();
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
                    $('#updateJudul').html('<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#updateHeader').removeClass('bg-success');
                    $('#updateHeader').addClass('bg-info');

                    $('#waktu').val(response.waktu);

                    $('#key').val(response.id);
                    $('#updateModal').modal('toggle');
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
                                setTable();
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
