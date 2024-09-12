@extends('layouts.user', ['page' => $page, 'sidebar' => true])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Approval'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter-date />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'"
                            :name="'SEARCH'" />
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
            $('#bulan').val("{{ date('n') }}").trigger('change');
            $('#tahun').val("{{ date('Y') }}").trigger('change');

            table = $('#table').DataTable({
                dom: '<"toolbar">flrtip',
                scrollY: screen.height * 0.6,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('user.approval.data') }}",
                    data: function(d) {
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();
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
                        Swal.close();
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
                order: [],
                pageLength: 50,
                lengthMenu: [
                    [50, 100, 500, -1],
                    [50, 100, 500, "All"]
                ],
            });
        })

        function approval(url, status) {
            Swal.fire({
                icon: 'question',
                title: 'Are you sure want to ' + status + ' this Data?',
                showCancelButton: true,
                confirmButtonText: 'Confirm ' + status,
                confirmButtonColor: status == 'APPROVE' ? '#08b92c' : '#dc3545'
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

        function poto(data) {
            $('#potoModal').modal('show');
            $('#tempatpoto').html('<img width="100%" height="100%" src="' + data + '" />')
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
