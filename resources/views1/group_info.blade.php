
@extends('layouts/app')

@section('title',__('Payment'))

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>
            <div class="step-form">
                <div class="group-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Stage - 01</th>
                                <th>Stage - 02</th>
                                <th>Stage - 03</th>
                                <th>Stage - 04</th>
                                <th>Stage - 05</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($groupInfoResult) && count($groupInfoResult)>0) 
                                @foreach($groupInfoResult as $key=>$data)
                                @php $val=\App\Models\User::where('parent_id',$data->id)->where('added_as','Spouse')->first(); @endphp
                                    <tr>
                                        <td>{{$key+1}}.</td>
                                        <td>{{$data->name}} {{$data->last_name}}</td>

                                        <td class="">
                                            @if($data->stage == 1)
                                                <div class="inprogress">In Process</div>
                                            @elseif($data->stage > 1)
                                                <div class="complete">Completed</div>
                                            @else
                                                <div class="pending">Pending</div>
                                            @endif
                                        </td>
                                        <td class="">
                                            @if($data->stage == 2)
                                                <div class="inprogress">In Process</div>
                                            @elseif($data->stage > 2)
                                                <div class="complete">Completed</div>
                                            @else
                                                <div class="pending">Pending</div>
                                            @endif</td>
                                        <td class="">
                                            @if($data->stage == 3)
                                                <div class="inprogress">In Process</div>
                                            @elseif($data->stage > 3)
                                                <div class="complete">Completed</div>
                                            @else
                                                <div class="pending">Pending</div>
                                            @endif
                                        </td>
                                        <td class="">
                                            @if($data->stage == 4)
                                                <div class="inprogress">In Process</div>
                                            @elseif($data->stage > 4)
                                                <div class="complete">Completed</div>
                                            @else
                                                <div class="pending">Pending</div>
                                            @endif
                                        </td>
                                        <td class="">
                                            @if($data->stage == 5)
                                                <div class="inprogress">In Process</div>
                                            @elseif($data->stage > 5)
                                                <div class="complete">Completed</div>
                                            @else
                                                <div class="pending">Pending</div>
                                            @endif
                                        </td>
                                        @if($val)
                                            <td class="plus plus-color"><span class="add">+</span> <span class="min">-</span> </td>

                                        @else
                                            <td ></td>
                                        @endif
                                    </tr>
                                    
                                    @if($val)
                                        
                                        <tr class="row-hide ChildData">
                                            <td></td>
                                            <td>{{$val->name}} {{$val->last_name}}</td>

                                            <td class="">
                                                @if($val->stage == 1)
                                                    <div class="inprogress">In Process</div>
                                                @elseif($val->stage > 1)
                                                    <div class="complete">Completed</div>
                                                @else
                                                    <div class="pending">Pending</div>
                                                @endif
                                            </td>
                                            <td class="">
                                                @if($val->stage == 2)
                                                    <div class="inprogress">In Process</div>
                                                @elseif($val->stage > 2)
                                                    <div class="complete">Completed</div>
                                                @else
                                                    <div class="pending">Pending</div>
                                                @endif</td>
                                            <td class="">
                                                @if($val->stage == 3)
                                                    <div class="inprogress">In Process</div>
                                                @elseif($val->stage > 3)
                                                    <div class="complete">Completed</div>
                                                @else
                                                    <div class="pending">Pending</div>
                                                @endif
                                            </td>
                                            <td class="">
                                                @if($val->stage == 4)
                                                    <div class="inprogress">In Process</div>
                                                @elseif($val->stage > 4)
                                                    <div class="complete">Completed</div>
                                                @else
                                                    <div class="pending">Pending</div>
                                                @endif
                                            </td>
                                            <td class="">
                                                @if($val->stage == 5)
                                                    <div class="inprogress">In Process</div>
                                                @elseif($val->stage > 5)
                                                    <div class="complete">Completed</div>
                                                @else
                                                    <div class="pending">Pending</div>
                                                @endif
                                            </td>
                                            
                                        </tr>
                                       
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="register-next">
                    <a href="{{url('profile')}}" class="main-btn bg-gray-btn">Next</a>
                </div>
            </div>
    </div>
    <!-- banner-end -->

    

@endsection


@push('custom_js')

<script>

    $('#PartialPaymentOnline').click(function(){
        $("#PartialPaymentOnlineDiv").toggle();
        $("#PartialPaymentOfflineDiv").css('display','none');
    });

    $('#PartialPaymentOffline').click(function(){
       
        $("#PartialPaymentOfflineDiv").toggle();
        $("#PartialPaymentOnlineDiv").css('display','none');
    });

    $('#FullPaymentOffline').click(function(){
        
        $("#FullPaymentOfflineDiv").toggle();
    });
    
    $("form#formSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                $('#formSubmit')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

        $(document).ready(function () {
            $(".plus").click(function () {
                $(this).toggleClass("minus");
                $(".ChildData").toggleClass("row-show");
                $(".ChildData").toggleClass("row-hide");
            });

        });
        $(document).ready(function () {
            $(".plus2").click(function () {
                $(this).toggleClass("minus");
                $(".three-row").toggleClass("row-hide2");
            });
        });

</script>
@endpush