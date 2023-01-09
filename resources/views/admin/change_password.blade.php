
@extends('layouts/master')

@section('title',__('Change Password'))

@section('Changepassword',__('active'))

@section('content')




<div class="container-fluid dashboard-default-sec">
    <div class="page-header">
        <div class="row">

            <div class="col-sm-6">
                <h3> Change Password</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"> Change Password</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-body">
                        <form id="form" action="" method="post" enctype="multipart/form-data">
                           @csrf
                           <div class="form-group col-md-12">
                              <label for="inputName">Enter Old Password <span class="text-danger">*</span></label>
                              <input required type="text"  style="width:50%" id="inputName" class="form-control" name="old_pass">
                           </div>
                           <div class="form-group col-md-12">
                              <label for="inputName">Enter New Password <span class="text-danger">*</span></label>
                              <input required type="text"  style="width:50%" id="inputName" class="form-control" name="password">
                           </div>
                           <div class="form-group col-md-12">
                              <label for="inputName">Enter Confirm  Password <span class="text-danger">*</span></label>
                              <input required type="text"  style="width:50%" id="inputName" class="form-control" name="confirm_password">
                           </div>
                           <div class="form-group col-md-12">
                              <input type="submit" class="btn btn-primary" value="Submit">
                           </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

