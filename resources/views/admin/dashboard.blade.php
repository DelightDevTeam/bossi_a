@extends('admin_layouts.app')
@section('content')
<div class="container card py-5 bg-secondary">
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mt-sm-0 mt-4">
            <div class="card  mb-2">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                        {{-- <i class="material-icons opacity-10">leaderboard</i> --}}
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Balance</p>
                        <h4 class="mb-0">{{ number_format(auth()->user()->balanceFloat) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span>latest update</p>
                </div>
            </div>
            <br>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card  mb-2">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                        {{-- <i class="material-icons opacity-10">weekend</i> --}}
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Deposit</p>
                        <h4 class="mb-0">{{ number_format($totalDeposit->amount/ 100, 2) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span>latest update</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <div class="card  mb-2">
                <div class="card-header p-3 pt-2 bg-transparent">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        {{-- <i class="material-icons opacity-10">store</i> --}}
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize ">WithDraw</p>
                        <h4 class="mb-0 ">{{ number_format(abs($totalWithdraw->amount)/ 100, 2) }}</h4>
                    </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                    <p class="mb-0 "><span class="text-success text-sm font-weight-bolder"></span>latest update</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <div class="card  mb-2">
                <div class="card-header p-3 pt-2 bg-transparent">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        {{-- <i class="material-icons opacity-10">store</i> --}}
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize ">TodayDeposit</p>
                        {{-- <h4 class="mb-0 ">{{ number_format(abs($todayDeposit->amount)/ 100, 2) }}</h4> --}}
                        <h4 class="mb-0 ">
                            {{ $todayDeposit ? number_format(abs($todayDeposit) / 100, 2) : '0.00' }}
                        </h4>

                    </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                    <p class="mb-0 "><span class="text-success text-sm font-weight-bolder"></span>latest update</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <div class="card  mb-2">
                <div class="card-header p-3 pt-2 bg-transparent">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        {{-- <i class="material-icons opacity-10">store</i> --}}
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize ">TodayWithdraw</p>
                        {{-- <h4 class="mb-0 ">{{ number_format(abs($todayWithdraw->amount)/ 100, 2) }}</h4> --}}
                        <h4 class="mb-0 ">
                            {{ $todayWithdraw ? number_format(abs($todayWithdraw) / 100, 2) : '0.00' }}
                        </h4>
                    </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                    <p class="mb-0 "><span class="text-success text-sm font-weight-bolder"></span>latest update</p>
                </div>
            </div>
        </div>
        @can('admin_access')
        <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <div class="card ">
                <div class="card-header p-3 pt-2 bg-transparent">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-info text-center border-radius-xl mt-n4 position-absolute">
                       <a href="{{route('admin.agent.create')}}">
                        {{-- <i class="material-icons opacity-10">person_add</i> --}}
                        <i class="fas fa-users"></i>
                    </a>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize ">Agents</p>
                        <h4 class="mb-0 ">{{$agent_count}}</h4>
                    </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                    <p class="mb-0 ">Just updated</p>
                </div>
            </div>
        </div>
        @endcan
        @can('agent_access')
        <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <div class="card ">
                <div class="card-header p-3 pt-2 bg-transparent">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                    <a href="{{route('admin.player.create')}}">
                        <i class="fas fa-users"></i>
                    </a>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize ">Players</p>
                        <h4 class="mb-0 ">{{$player_count}}</h4>
                    </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                    <p class="mb-0 ">Just updated</p>
                </div>
            </div>
        </div>
        @endcan
    </div>
    </div>
    @can('admin_access')
    <div class="row gx-4 mt-4">
        <div class="col-md-6">
            <div class="card">
                <form action="{{ route('admin.balanceUp') }}" method="post">
                    @csrf
                    <div class="card-header p-3 pb-0">
                        <h6 class="mb-1">Update Balance</h6>
                        <p class="text-sm mb-0">
                            Owner can update balance.
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="input-group input-group-static my-4">
                            <label>Amount</label>
                            <input type="integer" class="form-control" name="balance">
                        </div>

                        <button class="btn bg-gradient-dark mb-0 float-end">Update </button>
                    </div>
                </form>
            </div>
    </div>
    @endcan
</div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/chartjs.min.js"></script>
    <script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/jkanban/jkanban.js"></script>
    <script>
        var errorMessage = @json(session('error'));
        var successMessage = @json(session('success'));

        console.log(successMessage);
    </script>
    <script>
        @if(session() -> has('success'))
        Swal.fire({
            icon: 'success',
            title: successMessage,
            background: 'hsl(230, 40%, 10%)',
            showConfirmButton: false,
            timer: 1500
        })
        @elseif(session() -> has('error'))
        Swal.fire({
            icon: 'error',
            title: errorMessage,
            background: 'hsl(230, 40%, 10%)',
            showConfirmButton: false,
            timer: 1500
        })
        @endif
    </script>
    <script>
        var ctx2 = document.getElementById("chart-pie").getContext("2d");
        // Pie chart
        new Chart(ctx2, {
            type: "pie",
            data: {
                labels: ['Deposit', 'Withdraw'],
                datasets: [{
                    label: "Transaction",
                    weight: 9,
                    cutout: 0,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 1,
                    backgroundColor: ['#17c1e8', '#e91e63'],
                    data: [10, 20],
                    fill: false
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            color: '#c1c4ce5c'
                        },
                        ticks: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            color: '#c1c4ce5c'
                        },
                        ticks: {
                            display: false,
                        }
                    },
                },
            },
        });
    </script>
@endsection
