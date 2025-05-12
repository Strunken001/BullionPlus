@extends('admin.layouts.master')

@push('css')
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Dashboard'),
    ])
@endsection

@section('content')
    <div class="dashboard-area">
        <div class="dashboard-item-area">
            <div class="row">
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Add Money Balance') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }}{{ getAmount($data['recharge_money_total_balance'], 2) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Total') }}
                                        {{ getAmount($data['completed_recharge_money'] + $data['pending_recharge_money']) }}</span>
                                    <span class="badge badge--warning">{{ __('Pending') }}
                                        {{ getAmount($data['pending_recharge_money']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart7" data-percent="{{ $data['recharge_money_percent'] }}">
                                    <span>{{ getAmount($data['recharge_money_percent'],0)}}% </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Gift Card') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }}{{ getAmount($data['total_gift_card']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }}
                                        {{ get_default_currency_symbol() }}{{ getAmount($data['gift_card_this_month']) }}
                                    </span>
                                    <span class="badge badge--warning">{{ __('Last Month') }}
                                        {{ get_default_currency_symbol() }}{{ getAmount($data['gift_card_last_month']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Mobile Topup') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }}{{ getAmount($data['total_mobile_topup']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This month') }}
                                        {{ get_default_currency_symbol() }}{{ getAmount($data['mobile_topup_this_month']) }}
                                    </span>
                                    <span class="badge badge--warning">{{ __('Last month') }}
                                        {{ get_default_currency_symbol() }}{{ getAmount($data['mobile_topup_last_month']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Profit') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }}{{ getAmount($data['total_profits'],2) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }}
                                        {{ get_default_currency_symbol() }}
                                        {{ getAmount($data['this_month_profits']) }}</span>
                                    <span class="badge badge--warning">{{ __('Last Month') }}
                                        {{ get_default_currency_symbol() }}
                                        {{ getAmount($data['last_month_profits']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Total Users") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $data['total_users'] }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __("Verified") }} {{  $data['verified_users'] }}</span>
                                    <span class="badge badge--warning">{{ __("Unverified") }} {{ $data['unverified_users'] }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart12" data-percent="{{ $data['user_percent'] }}"><span>{{ round($data['user_percent'],2) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Total Supports Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $data['total_tickets'] }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __("active") }} {{  $data['active_tickets'] }}</span>
                                    <span class="badge badge--warning">{{ __("Pending") }} {{ $data['pending_tickets'] }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart15" data-percent="{{ $data['ticket_percent'] }}"><span>{{ round($data['ticket_percent'],2) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-area mt-15">
        <div class="row mb-15-none">
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">
                            {{ __('Monthly Add Money Chart') }}
                        </h5>
                        <a href="{{ setRoute('admin.add.money.index') }}" class="btn--base "> {{ __('View') }}</a>
                    </div>
                    <div class="chart-container">
                        <div id="chart1" data-chart_one_data="{{ json_encode($data['chart_one_data']) }}"
                            data-month_day="{{ json_encode($data['month_day']) }}" class="sales-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">
                            {{ __('Monthly Gift Card Chart') }}
                        </h5>
                        <a href="{{ setRoute('admin.gift.card.logs') }}" class="btn--base "> {{ __('View') }}</a>
                    </div>
                    <div class="chart-container">
                        <div id="chart2" data-chart_two_data="{{ json_encode($data['chart_two_data']) }}"
                            data-month_day="{{ json_encode($data['month_day']) }}" class="sales-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">
                            {{ __('Monthly Mobile Topup') }}
                        </h5>
                        <a href="{{ setRoute('admin.mobile.topup.index') }}" class="btn--base "> {{ __('View') }}</a>
                    </div>
                    <div class="chart-container">
                        <div id="chart3" data-chart_three_data="{{ json_encode($data['chart_three_data']) }}"
                            data-month_day="{{ json_encode($data['month_day']) }}" class="sales-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('User Analytics') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart4" data-chart_four_data="{{ json_encode($data['chart_four_data']) }}"
                            class="balance-chart"></div>
                    </div>
                    <div class="chart-area-footer">
                        <div class="chart-btn">
                            <a href="{{ setRoute('admin.users.index') }}"
                                class="btn--base w-100">{{ __('View Users') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __('Latest Transactions') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('TRX ID') }}</th>
                            <th>{{ __('Full Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Gateway') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['transactions'] ?? [] as $value)
                            <tr>
                                <td>{{ $value->trx_id }}</td>
                                <td><span>{{ $value->user->full_name }}</span></td>
                                <td>{{ $value->user->email }}</td>
                                <td>{{ $value->user->username }}</td>
                                <td>{{ $value->user->full_mobile }}</td>
                                <td>{{ $value->request_amount }}</td>
                                <td><span class="text--info">{{ $value->gateway_currency->gateway->name }}</span></td>
                                @if ($value->status === 1)
                                    <td><span class="badge badge--success">{{ __('Complete') }}</span></td>
                                @elseif ($value->status === 2)
                                    <td><span class="badge badge--warning">{{ __('Pending') }}</span></td>
                                @else
                                    <td><span class="badge badge--danger">{{ __('Canceled') }}</span></td>
                                @endif
                                <td>{{ $value->created_at }}</td>
                                <td>
                                    @include('admin.components.link.info-default', [
                                        'href' => setRoute('admin.add.money.details', $value->id),
                                        'permission' => 'admin.add.money.details',
                                    ])
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var chart1 = $('#chart1');
        var chart_one_data = chart1.data('chart_one_data');
        var month_day = chart1.data('month_day');
        // apex-chart
        var options = {
            series: [{
                name: "{{ __('Pending') }}",
                color: "#5A5278",
                data: chart_one_data.pending_data
            }, {
                name: "{{ __('Completed') }}",
                color: "#6F6593",
                data: chart_one_data.success_data
            }, {
                name: '{{ __('Canceled') }}',
                color: "#8075AA",
                data: chart_one_data.canceled_data
            }, {
                name: '{{ __('Hold') }}',
                color: "#A192D9",
                data: chart_one_data.hold_data
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                type: 'datetime',
                categories: month_day,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();

        var chart2 = $('#chart2');
        var chart_two_data = chart2.data('chart_two_data');
        console.log(chart2.data());
        var options = {
            series: [{
                name: "{{ __('Pending') }}",
                color: "#5A5278",
                data: chart_two_data.pending_data
            }, {
                name: "{{ __('Completed') }}",
                color: "#6F6593",
                data: chart_two_data.success_data
            }, {
                name: '{{ __('Canceled') }}',
                color: "#8075AA",
                data: chart_two_data.canceled_data
            }, {
                name: '{{ __('Hold') }}',
                color: "#A192D9",
                data: chart_two_data.hold_data
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 10
                },
            },
            yaxis: {
                type: 'datetime',
                labels: {
                    format: 'dd/MMM',
                },
                categories: month_day,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();

        var chart3 = $('#chart3');
        var chart_three_data = chart3.data('chart_three_data');
        var month_day = chart3.data('month_day');
        // apex-chart
        var options = {
            series: [{
                name: "{{ __('Pending') }}",
                color: "#5A5278",
                data: chart_three_data.pending_data
            }, {
                name: "{{ __('Completed') }}",
                color: "#6F6593",
                data: chart_three_data.success_data
            }, {
                name: '{{ __('Canceled') }}',
                color: "#8075AA",
                data: chart_three_data.canceled_data
            }, {
                name: '{{ __('Hold') }}',
                color: "#A192D9",
                data: chart_three_data.hold_data
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                type: 'datetime',
                categories: month_day,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart3"), options);
        chart.render()

        var chart4 = $('#chart4');
        var chart_four_data = chart4.data('chart_four_data');

        var options = {
            series: chart_four_data,
            chart: {
                width: 350,
                type: 'pie'
            },
            colors: ['#10c469', '#f03d30', '#ff9f43', '#A192D9'],
            labels: ['{{ __('Active') }}', '{{ __('Banned') }}', '{{ __('Unverified') }}', '{{ __('All') }}'],
            responsive: [{
                breakpoint: 1480,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 1199,
                options: {
                    chart: {
                        width: 380
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 575,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom'
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();
        // pie-chart
        $(function() {
            $('#chart7').easyPieChart({
                size: 80,
                barColor: '#10c469',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#10c4695a',
                lineCap: 'circle',
                animate: 3000
            });
        });
        $(function() {
            $('#chart12').easyPieChart({
                size: 80,
                barColor: '#5a5278',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#5a52785a',
                lineCap: 'circle',
                animate: 3000
            });
        });
        $(function() {
            $('#chart15').easyPieChart({
                size: 80,
                barColor: '#ADDDD0',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#ADDDD05a',
                lineCap: 'circle',
                animate: 3000
            });
        });
    </script>
@endpush
