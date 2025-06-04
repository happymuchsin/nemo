@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Approval'">
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
                        <th>Date</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Line</th>
                        <th>Style</th>
                        <th>Requestor</th>
                        <th>Request Type</th>
                        {{-- <th>Remark</th> --}}
                        {{-- <th>Fragment</th> --}}
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'poto'">
        <x-slot:body>
            <div class="form-group" id="tempatpoto"></div>
        </x-slot:body>
    </x-modal.modal>

    <script>
        var table = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#bulan').val("{{ date('n') }}").trigger('change');
            $('#tahun').val("{{ date('Y') }}").trigger('change');
            $('#filter_status').val('WAITING').trigger('change');

            table = initDataTable('table', '', '', '', {
                ajax: {
                    url: "{{ route('user.approval.data') }}",
                    data: function(d) {
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();
                        d.filter_status = $('#filter_status').val();
                    },
                },
                columns: [{
                        data: 'created_at'
                    },
                    {
                        data: 'needleBrand'
                    },
                    {
                        data: 'needleTipe'
                    },
                    {
                        data: 'needleSize'
                    },
                    {
                        data: 'line'
                    },
                    {
                        data: 'style'
                    },
                    {
                        data: 'requestor'
                    },
                    {
                        data: 'tipe'
                    },
                    // {
                    //     data: 'remark'
                    // },
                    // {
                    //     data: 'needle_status'
                    // },
                    {
                        data: 'gambar'
                    },
                    {
                        data: 'action'
                    },
                ],
            });
        })

        function approval(url, status) {
            customAlert({
                icon: 'question',
                title: 'Are you sure want to ' + status + ' this Data?',
                showCancelButton: true,
                confirmButtonText: 'Confirm ' + status,
                confirmButtonColor: status == 'APPROVE' ? '#08b92c' : '#dc3545',
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

        function poto(data) {
            $('#potoModal').modal('show');
            $('#tempatpoto').html('<img width="100%" height="100%" src="' + data + '" />')
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
