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
                warningAlert('Please insert Password');
            } else {
                sendAjax('', {
                    url: "{{ route('admin.profile.change') }}",
                    type: "POST",
                    data: {
                        password: $('#password').val(),
                    },
                    success: function(response) {
                        successAlert(response);
                        closeAlert();
                        $('#password').val('');
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            }
        }
    </script>
@endsection
