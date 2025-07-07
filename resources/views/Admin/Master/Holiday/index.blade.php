@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Holiday'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :label="'Year'" :id="'tahun'" :tipe="'select'" :alloption="false">
                        <x-slot:option>
                            @for ($x = date('Y'); $x >= 2023; $x--)
                                <option value="{{ $x }}">{{ $x }}</option>
                            @endfor
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :label="'Month'" :id="'bulan'" :tipe="'select'" :alloption="false">
                        <x-slot:option>
                            @for ($mm = 1; $mm <= 12; $mm++)
                                <option value="{{ $mm }}">
                                    {{ date('F', mktime(0, 0, 0, $mm, 1)) }}</option>;
                            @endfor
                        </x-slot:option>
                    </x-filter.filter>

                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'reloadCalendar()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <div id="calendar"></div>
        </x-slot:body>
    </x-layout.content>

    <script>
        var calendar = null;
        $(document).ready(function() {
            $('#bulan').val("{{ date('n') }}").trigger('change');
            $('#tahun').val("{{ date('Y') }}").trigger('change');
            reloadCalendar();
        })

        function reloadCalendar() {
            sendAjax('', {
                url: "{{ route('admin.master.holiday.calendar', ['locale' => app()->getLocale()]) }}",
                type: "post",
                data: {
                    bulan: $('#bulan').val(),
                    tahun: $('#tahun').val(),
                },
                success: function(response) {
                    unwaitAlert();
                    if (calendar) {
                        calendar.destroy();
                    }
                    var calendarEl = document.getElementById('calendar');

                    calendar = new FullCalendar.Calendar(calendarEl, {
                        height: screen.height * 0.6,
                        showNonCurrentDates: false,
                        fixedWeekCount: false,
                        eventDidMount: function(info) {
                            // Misalnya kita ubah tampilan kotak tanggal tempat event muncul
                            const eventDate = info.event.startStr;

                            // Cari elemen cell berdasarkan tanggal
                            const cell = document.querySelector(`[data-date="${eventDate}"]`);

                            if (cell) {
                                // Contoh: Ubah background cell
                                cell.style.backgroundColor = 'pink';
                            }
                            tippy(info.el, {
                                // content: info.event.extendedProps.description,
                                content: info.event.title
                            });
                        },
                        dateClick: function(info) {
                            crup(info.dateStr);
                        },
                        headerToolbar: {
                            left: '',
                            center: '',
                            right: 'title'
                        },
                        eventTimeFormat: {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false,
                        },
                        events: response.events,
                    });

                    calendar.render();

                    calendar.gotoDate(new Date(response.first));
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function crup(tanggal) {
            customAlert({
                icon: 'question',
                title: "Set Holiday for " + tanggal,
                showCancelButton: true,
                confirmButtonText: "Confirm",
                confirmButtonColor: '#08fe3e',
                cancelButtonText: "Cancel",
                input: "text",
                inputPlaceholder: "Description",
                callback: function(result) {
                    var n = 0;
                    if (result == '') {
                        warningAlert('Please enter Description');
                    } else {
                        n = 1;
                    }

                    if (n == 1) {
                        sendAjax('', {
                            url: "{{ route('admin.master.holiday.crup', ['locale' => app()->getLocale()]) }}",
                            type: "POST",
                            data: {
                                'tanggal': tanggal,
                                'description': result,
                            },
                            success: function(response) {
                                successAlert(response);
                                closeAlert();
                                reloadCalendar();
                            },
                            error: function(response) {
                                warningAlert(response.responseText);
                            }
                        })
                    }
                }
            })
        };
    </script>
@endsection
