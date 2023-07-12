
    <style>
        .fs-dropdown {
            width: 100%;
        }

    </style>
    <div class="approved-section">
        <label for="inputName">User Type : {{\App\Helpers\commonHelper::getDesignationName($user->designation_id)}}</label>
        @php $userType = [6,4]; @endphp
        <div class="form-group">
            <div class="form-line">
                <label for="inputName">Select Payment Country </label>
                <select name="citizenship" class="form-control test" id="paymentCitizenship">
                    <option value="">Select</option>
                    @if(!empty($country))
                        @foreach($country as $key=>$val)
                            <option @if($user && $citizenship == $val->country_id) selected @endif value="{{$val->country_id}}" >{{$val->country_name}}</option>
                        @endforeach

                    @endif
                    
                </select>
            </div>
        </div>

        <div class="form-group" style="display: @if($user->designation_id == '3')) block @else none @endif">
            <div class="form-line">
                <label for="inputName">Select Volunteer Role </label>
                <select name="role_id[]" class="form-control test" id="role_id" multiple>
                    
                    @php $roles = \App\Models\Role::orderBy('name','Asc')->where('id','!=','1')->where('status','1')->get(); @endphp
                    @if(!empty($roles))
                        @foreach($roles as $key=>$role)
                            <option value="{{$role->id}}" >{{$role->name}}</option>
                        @endforeach

                    @endif
                    
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="form-line">
                <label for="inputName">@lang('admin.amount') <label class="text-danger">*</label></label>
                <input type="number" class="form-control" name="base_amount" placeholder="Enter amount value" value="{{$basePrice}}" readonly required>
            </div>
        </div>
        
            <div class="form-group" style="display: @if(!in_array($user->designation_id,$userType)) block @else none @endif">
                <div class="form-line">
                    <label for="inputName">Do you want to change User Room Type</label>
                    <select name="room_type" class="form-control" id="change_room_type" required>
                        <option value="Yes" >Yes</option>
                        <option value="No" selected>No</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="CategoryDiv" style="display:none">
                <div class="form-line">
                    <label for="inputName">Select Category <label class="text-danger">*</label></label>
                    <select name="category" class="form-control" id="selectCategory">
                        <option value="">Select</option>
                        @if(!empty($category))
                            @foreach($category as $key=>$val)
                                <option value="{{$key}}" data-amount="{{$val}}">{{$key}}</option>
                            @endforeach

                        @endif
                        
                    </select>
                </div>
            </div>
       
            <!--         
            <div class="form-group">
                <div class="form-line">
                    <label for="inputName">Allow Cash payment <label class="text-danger">*</label></label>
                    <select name="cash_payment" class="form-control" required id="cash_payment" >
                        <option value="No" selected >No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            </div> -->
            
            <div class="form-group" style="display: @if(!in_array($user->designation_id,$userType)) block @else none @endif">
                <div class="form-line">
                    @if (strtotime(date('Y-m-d H:i:s')) > strtotime('2023-07-01 05:00:00'))
                        <label for="inputName">Apply June Offer  <label class="text-danger">*</label></label>
                    @else
                        <label for="inputName">Apply Early Bird <label class="text-danger">*</label></label>
                    @endif
                    <select name="early_bird" class="form-control" required id="early_bird" @if (date('Y-m-d') >= date('2023-07-01')) disabled @endif>
                        <option value="No" selected >No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            </div>

        
            <div class="form-group" id="OffersDiv" style="display: @if(!in_array($user->designation_id,$userType)) block @else none @endif">
                <div class="form-line">
                    <label for="inputName">Select Offers </label>
                    <select name="offer_id" class="form-control" id="selectOffer">
                        <option value="">Select</option>
                        @if(!empty($Offers))
                            @foreach($Offers as $data)
                                <option value="{{$data->id}}" >{{$data->name}}</option>
                            @endforeach

                        @endif
                    
                    </select>
                </div>
            </div>

        <div class="form-group">
            <div class="form-line">
                <label for="inputName">Payable Amount <label class="text-danger">*</label></label>
                <input type="number" class="form-control" name="amount" id="payable_amount" value="{{in_array($user->designation_id,$userType) ? '0' : $basePrice }}" readonly required>
            </div>
        </div>
    </div>

    <script>

    $(document).ready(function() {
        var payable_amount = $('#payable_amount').val();
        var base_amount = "{{$basePrice}}";
        var finAmount = payable_amount;
            
        $('#change_room_type').change(function(){

            if(this.value == 'Yes'){
                
                $('#CategoryDiv').css('display','block');
                $('#selectCategory').attr('required',true);
                $('#selectCategory').val("");
                $('#selectOffer').val('');

            }else{
                
                $('#CategoryDiv').css('display','none');
                $('#selectCategory').attr('required',false);
                
                $('#selectCategory').val("");
                $('#early_bird').val("No");
                $('#selectOffer').val('');
                if($('#early_bird').val() == 'Yes'){

                    $('#payable_amount').val((parseInt(base_amount)-parseInt(100)));
                    finAmount = (parseInt(base_amount)-parseInt(100));

                }else{

                    $('#payable_amount').val(base_amount);
                    finAmount = base_amount;
                }

               
                

            }
            
        });

        $('#selectCategory').change(function(){

            $('#early_bird').val("No");
            $('#selectOffer').val('');
            if($('#early_bird').val() == 'Yes'){

                $('#payable_amount').val((parseInt(base_amount)-parseInt(100)));
                finAmount = (parseInt(base_amount)-parseInt(100));

            }else{

                $('#payable_amount').val(base_amount);
                finAmount = base_amount;
            }

            if(this.value == 'Upgrade to Single Deluxe Room'){
                
                var amount = $(this).find(':selected').attr('data-amount');

                finAmount=parseInt(parseInt(payable_amount)+parseInt(amount));

                if(finAmount <= 1075){
                    finAmount = 1075;
                }
                
               
            }else if(this.value == 'Twin Sharing Deluxe Room'){
                
                var amount = $(this).find(':selected').attr('data-amount');

                finAmount=parseInt(amount);

               
            }else if(this.value == 'Upgrade to Club Floor'){
                
                var amount = $(this).find(':selected').attr('data-amount');
                
                finAmount=(parseInt(payable_amount)+parseInt(amount));

                @if($trainer == 'No')

                    if(finAmount <= 2625){
                        finAmount = 2625;
                    }

                @elseif($trainer == 'Yes')

                    if(finAmount <= 1950){
                        finAmount = 1950;
                    }

                @else

                    if(finAmount <= 1375){
                        finAmount = 1375;
                    }

                @endif
                

               
            }else if(this.value == 'Upgrade to Suite'){

                var amount = $(this).find(':selected').attr('data-amount');
                
                finAmount = (parseInt(payable_amount)+parseInt(amount));

                @if($trainer == 'No')

                    if(finAmount <= 3225){
                        finAmount = 3325;
                    }

                @elseif($trainer == 'Yes')

                    if(finAmount <= 3050){
                        finAmount = 3050;
                    }

                @else

                    if(finAmount <= 2075){
                        finAmount = 2075;
                    }

                @endif

               
            }else if(this.value == 'Day pass'){
                
                var amount = $(this).find(':selected').attr('data-amount');
                
                finAmount = (parseInt(payable_amount)+parseInt(amount));
                
            }

            $('#payable_amount').val(finAmount);
            
        });

        
        $('#early_bird').change(function(){

            $('#selectOffer').val('');
            if(this.value == 'Yes'){
                
                
                var selectCategory = $('#selectCategory').val();
                
                if(selectCategory == 'Upgrade to Single Deluxe Room'){

                    if(finAmount > 975){

                        finAmount = (parseInt(finAmount)-parseInt(100));
                    }

                }else if(selectCategory == 'Upgrade to Club Floor'){

                    if(finAmount > 1275){

                        finAmount = (parseInt(finAmount)-parseInt(100));
                    }

                }else if(selectCategory == 'Upgrade to Suite'){

                    if(finAmount >= 2075){

                        finAmount = (parseInt(finAmount)-parseInt(100));
                    }

                }else{

                    @if($trainer == 'Yes')

                        finAmount = (parseInt(finAmount)-parseInt(200));

                    @else

                        finAmount = (parseInt(finAmount)-parseInt(100));

                    @endif

                    
                }
                
                $('#payable_amount').val(finAmount);
               
            }else {
                
                
                var selectCategory = $('#selectCategory').val();
                
                if(selectCategory == 'Upgrade to Single Deluxe Room'){

                    if(finAmount >= 975){

                        finAmount = (parseInt(finAmount)+parseInt(100));
                    }

                }else{

                    @if($trainer == 'Yes')

                        if(selectCategory == 'Upgrade to Club Floor' || selectCategory == 'Upgrade to Suite'){

                            finAmount = (parseInt(finAmount)+parseInt(100)); 
                        }else{
                            finAmount = (parseInt(finAmount)+parseInt(200));
                        }
                        

                    @else

                        finAmount = (parseInt(finAmount)+parseInt(100));

                    @endif
                }

                $('#payable_amount').val(finAmount);
               
            }
            
        });
        
        $('#cash_payment').change(function(){

            
            if(this.value == 'Yes'){

                $('#early_bird').attr('disabled',true);
                $('#early_bird').val('No');
                $('#selectOffer').val('');

                finAmount = (parseInt(finAmount)+parseInt(100));
                $('#payable_amount').val(finAmount);
               
            }else {
                
                $('#early_bird').attr('disabled',false);
            }
            
        });

        
        $('#selectOffer').change(function(){

            payable_amount = $('#payable_amount').val();

            if(this.value){
                $.ajax({

                    type: "POST",
                    dataType: "json",
                    url: "{{url('admin/user/get-offer-price')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': this.value,
                        'amount': finAmount,
                    },
                    beforeSend: function() {
                        $('#preloader').css('display', 'block');
                    },
                    error: function(xhr, textStatus) {

                        if (xhr && xhr.responseJSON.message) {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                        }
                        $('#preloader').css('display', 'none');
                    },
                    success: function(data) {
                        $('#preloader').css('display', 'none');
                        if(data.error){
                            sweetAlertMsg('error', data.message);
                        }else{
                            $('#payable_amount').val(data.amount);
                        }
                    
                    }
                });
            }else{
                
                $('#payable_amount').val(finAmount);
            }
            

        });
        
        
        $('#paymentCitizenship').change(function(){

            citizenship = $(this).val();

            if(citizenship != '{{$citizenship}}'){

                $.ajax({

                    type: "POST",
                    dataType: "json",
                    url: "{{url('admin/user/get-profile-base-price')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': '{{$user->id}}',
                        'citizenship': citizenship,
                    },
                    beforeSend: function() {
                        $('#preloader').css('display', 'block');
                    },
                    error: function(xhr, textStatus) {

                        if (xhr && xhr.responseJSON.message) {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                        }
                        $('#preloader').css('display', 'none');
                    },
                    success: function(data) {
                        $('#preloader').css('display', 'none');
                        $('#ProfileStatusData').html(data.html);
                    
                    }
                });

            }else{
                
                $('#payable_amount').val(finAmount);
            }
            

        });

        $('.test').fSelect({
            placeholder: "-- Select -- ",
            numDisplayed: 5,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
        });
        
    });
</script>