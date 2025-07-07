@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Dashboard'">
        <x-slot:body>
            <div class="row">
                <div class="col-sm-2">
                    <div class="small-box" style="background-color: #dd3849;margin-bottom:0px;">
                        <h4 class="text-bold text-center text-white">OUTSTANDING</h4>
                    </div>
                    <div class="small-box" style="background-color: #ff5c42">
                        <div class="inner" id="outstanding" style="height: 200pt;"></div>
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="row">
                        <x-layout.small-box :innerStyle="'color:black;background-color: #3698d8'" :footerStyle="'color:black'" :id="'ava_single_needle'" :inner="'Single Needle'" :icon="'fa-shelves'" :footer="'Available Stock for Usage'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #25cb76'" :footerStyle="'color:black'" :id="'ava_obras'" :inner="'Obras'" :icon="'fa-shelves'" :footer="'Available Stock for Usage'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #e67f32'" :footerStyle="'color:black'" :id="'ava_double_needle'" :inner="'Double Needle'" :icon="'fa-shelves'" :footer="'Available Stock for Usage'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #e84e42'" :footerStyle="'color:black'" :id="'ava_kansai'" :inner="'Kansai'" :icon="'fa-shelves'" :footer="'Available Stock for Usage'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #85c1e7'" :footerStyle="'color:black'" :id="'rep_single_needle'" :inner="'Single Needle'" :icon="'fa-arrows-rotate'" :footer="'Replacement Current Month'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #80dfac'" :footerStyle="'color:black'" :id="'rep_obras'" :inner="'Obras'" :icon="'fa-arrows-rotate'" :footer="'Replacement Current Month'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #f0b27f'" :footerStyle="'color:black'" :id="'rep_double_needle'" :inner="'Double Needle'" :icon="'fa-arrows-rotate'" :footer="'Replacement Current Month'" />
                        <x-layout.small-box :innerStyle="'color:black;background-color: #f1958c'" :footerStyle="'color:black'" :id="'rep_kansai'" :inner="'Kansai'" :icon="'fa-arrows-rotate'" :footer="'Replacement Current Month'" />
                    </div>
                </div>
            </div>
            <div id="chart" style="width: 100%; height: 500pt;"></div>
        </x-slot:body>
    </x-layout.content>

    <script>
        var chart = null;
        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            // $('#collSidebar').click();

            outstanding();

            reloadData();

            setChart();
        })

        function outstanding() {
            sendAjax('', {
                url: "{{ route('user.dashboard.data') }}",
                type: "POST",
                data: {
                    tipe: 'outstanding',
                },
                success: function(response) {
                    unwaitAlert();
                    $('#outstanding').html(response);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function reloadData() {
            sendAjax('', {
                url: "{{ route('user.dashboard.data') }}",
                type: "POST",
                data: {
                    tipe: 'box',
                },
                success: function(response) {
                    unwaitAlert();
                    $('#ava_single_needle').text(response.ava_single_needle);
                    $('#ava_obras').text(response.ava_obras);
                    $('#ava_double_needle').text(response.ava_double_needle);
                    $('#ava_kansai').text(response.ava_kansai);
                    $('#rep_single_needle').text(response.rep_single_needle);
                    $('#rep_obras').text(response.rep_obras);
                    $('#rep_double_needle').text(response.rep_double_needle);
                    $('#rep_kansai').text(response.rep_kansai);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function setChart() {
            if (chart != null) {
                chart.dispose();
            }
            sendAjax('', {
                url: "{{ route('user.dashboard.data') }}",
                type: "POST",
                data: {
                    tipe: 'chart',
                },
                success: function(response) {
                    unwaitAlert();
                    am4core.ready(function() {
                        am4core.useTheme(am4themes_animated);
                        var chart = am4core.create("chart", am4charts.XYChart);
                        var x = [];
                        $.each(response, function(k, v) {
                            x.push({
                                'date': v.date,
                                'ava_single_needle': v.ava_single_needle,
                                'ava_double_needle': v.ava_double_needle,
                                'ava_obras': v.ava_obras,
                                'ava_kansai': v.ava_kansai,
                                'rep_single_needle': v.rep_single_needle,
                                'rep_double_needle': v.rep_double_needle,
                                'rep_obras': v.rep_obras,
                                'rep_kansai': v.rep_kansai,
                            });
                        })
                        chart.data = x;
                        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                        categoryAxis.dataFields.category = "date";

                        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

                        function createSeries(field, name, color) {
                            var series = chart.series.push(new am4charts.LineSeries());
                            series.dataFields.valueY = field;
                            series.dataFields.categoryX = "date";
                            series.name = name;
                            series.tooltipText = "{name}: [b]{valueY}[/]";
                            series.stroke = am4core.color(color);
                            series.strokeWidth = 2;

                            var bullet = series.bullets.push(new am4charts.CircleBullet());
                            bullet.circle.stroke = am4core.color(color);
                            bullet.circle.strokeWidth = 2;

                            return series;
                        }

                        var series1 = createSeries("void", "Toggle All", "#fff");
                        var series2 = createSeries("void", "Toggle Single Needle", "#fff");
                        var series3 = createSeries("void", "Toggle Obras", "#fff");
                        var series4 = createSeries("void", "Toggle Double Needle", "#fff");
                        var series5 = createSeries("void", "Toggle Kansai", "#fff");
                        var series6 = createSeries("ava_single_needle", "Available Single Needle",
                            "#3698d8");
                        var series7 = createSeries("rep_single_needle", "Replacement Single Needle",
                            "#85c1e7");
                        var series8 = createSeries("ava_obras", "Available Obras", "#25cb76");
                        var series9 = createSeries("rep_obras", "Replacement Obras", "#80dfac");
                        var series10 = createSeries("ava_double_needle", "Available Double Needle",
                            "#e67f32");
                        var series11 = createSeries("rep_double_needle", "Replacement Double Needle",
                            "#f0b27f");
                        var series12 = createSeries("ava_kansai", "Available Kansai", "#e84e42");
                        var series13 = createSeries("rep_kansai", "Replacement Kansai", "#f1958c");

                        // All
                        series1.events.on("hidden", function() {
                            series2.hide();
                            series3.hide();
                            series4.hide();
                            series5.hide();
                            series6.hide();
                            series7.hide();
                            series8.hide();
                            series9.hide();
                            series10.hide();
                            series11.hide();
                            series12.hide();
                            series13.hide();
                        });

                        series1.events.on("shown", function() {
                            series6.show();
                            series7.show();
                            series8.show();
                            series9.show();
                            series10.show();
                            series11.show();
                            series12.show();
                            series13.show();
                        });
                        // Single Needle
                        series2.events.on("hidden", function() {
                            series1.hide();
                            series3.hide();
                            series4.hide();
                            series5.hide();
                            series6.hide();
                            series7.hide();
                            series8.hide();
                            series9.hide();
                            series10.hide();
                            series11.hide();
                            series12.hide();
                            series13.hide();
                        });

                        series2.events.on("shown", function() {
                            series6.show();
                            series7.show();
                        });
                        // Obras
                        series3.events.on("hidden", function() {
                            series2.hide();
                            series1.hide();
                            series4.hide();
                            series5.hide();
                            series6.hide();
                            series7.hide();
                            series8.hide();
                            series9.hide();
                            series10.hide();
                            series11.hide();
                            series12.hide();
                            series13.hide();
                        });

                        series3.events.on("shown", function() {
                            series8.show();
                            series9.show();
                        });
                        // Double Needle
                        series4.events.on("hidden", function() {
                            series2.hide();
                            series3.hide();
                            series1.hide();
                            series5.hide();
                            series6.hide();
                            series7.hide();
                            series8.hide();
                            series9.hide();
                            series10.hide();
                            series11.hide();
                            series12.hide();
                            series13.hide();
                        });

                        series4.events.on("shown", function() {
                            series10.show();
                            series11.show();
                        });
                        // Kansai
                        series5.events.on("hidden", function() {
                            series2.hide();
                            series3.hide();
                            series4.hide();
                            series1.hide();
                            series6.hide();
                            series7.hide();
                            series8.hide();
                            series9.hide();
                            series10.hide();
                            series11.hide();
                            series12.hide();
                            series13.hide();
                        });

                        series5.events.on("shown", function() {
                            series12.show();
                            series13.show();
                        });


                        chart.legend = new am4charts.Legend();
                        chart.cursor = new am4charts.XYCursor();
                    })
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        socket.on('nemoReload', () => {
            outstanding();

            reloadData();

            setChart();
        })
    </script>
@endsection
