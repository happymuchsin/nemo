@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Daily Closing'">
        <x-slot:body>
            <x-modal.body :tipe="'text'" :label="'Range Date'" :id="'range_date'" />
            <h3><span class="badge badge-danger">Please note that this action cannot be undone, and the data cannot be recovered. Please proceed with caution.</span></h3>
            <x-layout.button :class="'btn-primary'" :id="'recalculate'" :onclick="'recalculate()'" :icon="'fa fa-arrow-rotate-right'" :name="'RECALCULATE'" />
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null;
        $(document).ready(function() {
            $('#range_date').val("{{ date('Y-m-d') . ' - ' . date('Y-m-d') }}")

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
        })

        function recalculate() {
            customAlert({
                icon: 'question',
                title: "Are you sure want to recalculate the data?",
                showCancelButton: true,
                confirmButtonText: "Confirm Recalculate",
                confirmButtonColor: '#dc3545',
                cancelButtonText: "Cancel",
                callback: function() {
                    sendAjax('', {
                        url: "{{ route('admin.tools.daily-closing.save') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'recalculate',
                            range_date: $('#range_date').val(),
                        },
                        success: function(response) {
                            successAlert(response);
                            closeAlert();
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
