@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Profile'">
        <x-slot:body>
            <x-modal.body :tipe="'password'" :label="'Change Password'" :id="'password'" />
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'ubah()'" :icon="'fa fa-save'" :name="'Save'" />
        </x-slot:body>
    </x-layout.content>

    <script>
        function ubah() {
            if ($('#password').val() == '') {
                Swal.fire('Warning', 'Please insert Password', 'warning');
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.tools.profile.change') }}",
                    data: {
                        password: $('#password').val(),
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
                        Swal.fire('Success!', response, 'success');
                        setTimeout(() => {
                            Swal.close();
                        }, 1000);
                        $('#password').val('');
                    },
                    error: function(response) {
                        Swal.fire('Warning!', response.responseText, 'warning');
                    }
                })
            }
        }
    </script>
@endsection
