@extends('admin_layouts.app')
@section('styles')
    <style>
        .transparent-btn {
            background: none;
            border: none;
            padding: 0;
            outline: none;
            cursor: pointer;
            box-shadow: none;
            appearance: none;
            /* For some browsers */
        }


        .custom-form-group {
            margin-bottom: 20px;
        }

        .custom-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .custom-form-group input,
        .custom-form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        .custom-form-group input:focus,
        .custom-form-group select:focus {
            border-color: #d33a9e;
            box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
        }

        .submit-btn {
            background-color: #d33a9e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        .submit-btn:hover {
            background-color: #b8328b;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css">
@endsection
@section('content')

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <h4>Deposit Request</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-form-group">
                                <label class="form-label">User Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $deposit->user->name }}"
                                       readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-form-group">
                                <label class="form-label">Amount</label>
                                <input type="text" class="form-control" name="amount" value="{{ $deposit->amount }}"
                                       readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-form-group">
                                <label class="form-label">Bank Account Name</label>
                                <input type="text" class="form-control" name="account_name"
                                       value="{{ $deposit->bank->account_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-form-group"><label class="form-label">Bank Account No</label>
                                <input type="text" class="form-control" name="account_no"
                                       value="{{ $deposit->bank->account_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-form-group">
                                <label class="form-label">Payment Method</label>
                                <input type="text" class="form-control" name=""
                                       value="{{ $deposit->bank->paymentType->name }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <img src="{{asset('assets/img/deposit/'. $deposit->image) }}" class="img-fluid rounded" alt="">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
