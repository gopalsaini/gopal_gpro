
    <style>
        .fs-dropdown {
            width: 100%;
        }

    </style>
    <div class="approved-section">
        <label for="inputName">User Type : {{\App\Helpers\commonHelper::getDesignationName($user->designation_id)}}</label><br>
        <label for="inputName">Amount Paid by user : {{$user->amount}}</label><br>
        
        @php $spouseResult=\App\Models\User::where('parent_id',$user->id)->where('added_as','Spouse')->first(); 

        @endphp

        @if($spouseResult)

            <label for="inputName">Spouse : {{$spouseResult->name}} {{$spouseResult->last_name}}</label><br>
        @endif
        <label for="inputName">Current Room Category : {{$user->upgrade_category ?? ($user->room ?? 'Single')}}</label><br><br>
        @php 
            $userType = [6,4];  $room= ''; $roomKey = 0;

            if($user->room == null || $user->room == 'Single'){
                $room= 'Upgrade to Single Deluxe Room';
            }elseif($user->room == 'Sharing'){

                $room= 'Twin Sharing Deluxe Room';
            }else{

                $room= $user->upgrade_category;
            }

            $keys = array_keys($category);


        @endphp
       

            <!-- <div class="form-group">
                <div class="form-line">
                    <label for="inputName">@lang('admin.amount') <label class="text-danger">*</label></label>
                    <input type="number" class="form-control" name="base_amount" placeholder="Enter amount value" value="{{ $basePrice}}" readonly required>
                </div>
            </div> -->
        
            <div class="form-group" style="display: @if(!in_array($user->designation_id,$userType)) block @else none @endif">
                <div class="form-line">
                    <label for="inputName">Do you want to change User Room Type: </label>
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
                        <option value="">Select </option>
                        

                        @if(!empty($category) && count($category)>0)

                            @if($room == 'Twin Sharing Deluxe Room')

                                @if(isset($keys[0]) && $keys[0] == 'Upgrade to Single Deluxe Room')
                                    <option value="{{$keys[0]}}" data-amount="{{$category['Upgrade to Single Deluxe Room']}}">{{$keys[0]}}</option>
                                @endif
                                @if(isset($keys[1]) && $keys[1] == 'Upgrade to Club Floor' && \App\Helpers\commonHelper::countClubFloorRoom() == false)
                                    <option value="{{$keys[1]}}" data-amount="{{$category['Upgrade to Club Floor']}}">{{$keys[1]}}</option>
                                @endif
                                @if(isset($keys[2]) && $keys[2] == 'Upgrade to Suite' && \App\Helpers\commonHelper::countSuiteRoom() == false)
                                    <option value="{{$keys[2]}}" data-amount="{{$category['Upgrade to Suite']}}">{{$keys[2]}}</option>
                                @endif
                                @if(isset($keys[3]) && $keys[3] == 'Day pass')
                                    <option value="{{$keys[3]}}" data-amount="{{$category['Day pass']}}">{{$keys[3]}}</option>
                                @endif
                                
                            @elseif($room == 'Upgrade to Single Deluxe Room')

                                @if(isset($keys[0]) && $keys[0] == 'Upgrade to Club Floor' && \App\Helpers\commonHelper::countClubFloorRoom() == false)
                                    <option value="{{$keys[0]}}" data-amount="{{$category['Upgrade to Club Floor']}}">{{$keys[0]}}</option>
                                @endif
                                @if(isset($keys[1]) && $keys[1] == 'Upgrade to Suite' && \App\Helpers\commonHelper::countSuiteRoom() == false)
                                    <option value="{{$keys[1]}}" data-amount="{{$category['Upgrade to Suite']}}">{{$keys[1]}}</option>
                                @endif
                                @if(isset($keys[2]) && $keys[2] == 'Day pass')
                                    <option value="{{$keys[2]}}" data-amount="{{$category['Day pass']}}">{{$keys[2]}}</option>
                                @endif


                                @if(isset($keys[1]) && $keys[1] == 'Upgrade to Club Floor' && \App\Helpers\commonHelper::countClubFloorRoom() == false)
                                    <option value="{{$keys[1]}}" data-amount="{{$category['Upgrade to Club Floor']}}">{{$keys[1]}}</option>
                                @endif
                                @if(isset($keys[2]) && $keys[2] == 'Upgrade to Suite' && \App\Helpers\commonHelper::countSuiteRoom() == false)
                                    <option value="{{$keys[2]}}" data-amount="{{$category['Upgrade to Suite']}}">{{$keys[2]}}</option>
                                @endif
                                @if(isset($keys[3]) && $keys[3] == 'Day pass')
                                    <option value="{{$keys[3]}}" data-amount="{{$category['Day pass']}}">{{$keys[3]}}</option>
                                @endif
                                
                            @elseif($room == 'Upgrade to Club Floor')

                                @if(isset($keys[1]) && $keys[1] == 'Upgrade to Suite' && \App\Helpers\commonHelper::countSuiteRoom() == false)
                                    <option value="{{$keys[1]}}" data-amount="{{$category['Upgrade to Suite']}}">{{$keys[1]}}</option>
                                @endif
                                

                            @endif
                                
                            
                                
                        @endif
                                    
                    </select>
                    
                </div>
            </div>
       
            <div class="form-group" id="AmountDiv" style="display:none">
                <div class="form-line">
                    <label for="inputName">Additional  amount to be paid by user: <label class="text-danger">*</label></label>
                    <input type="number" class="form-control" name="amount" id="payable_amount" value="{{in_array($user->designation_id,$userType) ? '0' :  ($basePrice <= 0 ? 0 : $basePrice) }}" readonly required>
                </div>
            </div>
    </div>

    <script>

    $(document).ready(function() {
        var payable_amount = $('#payable_amount').val();
        var base_amount = "{{$basePrice}}";
        var user_amount = "{{$userAmount}}";
        var early_bird = "{{$user->early_bird}}";
        var finAmount = payable_amount;
            
        $('#change_room_type').change(function(){

            if(this.value == 'Yes'){
                
                $('#CategoryDiv').css('display','block');
                $('#AmountDiv').css('display','block');
                $('#RemarkDibRoomUpGrade').css('display','block');
                $('#selectCategory').attr('required',true);
                $('#SubmitButtonRoomUpgrade').attr('disabled',false);
                $('#selectCategory').val("");
                $('#selectOffer').val('');

            }else{
                
                $('#CategoryDiv').css('display','none');
                $('#AmountDiv').css('display','none');
                $('#RemarkDibRoomUpGrade').css('display','none');
                $('#selectCategory').attr('required',false);
                $('#SubmitButtonRoomUpgrade').attr('disabled',true);
                
                $('#selectCategory').val("");
                $('#selectOffer').val('');
                

                $('#payable_amount').val(base_amount);
                finAmount = base_amount;
                

               
                

            }
            
        });

        $('#selectCategory').change(function(){

            $('#selectOffer').val('');
            
            $('#payable_amount').val(base_amount);
            finAmount = base_amount;
           
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

            finAmount = (parseInt(finAmount)-parseInt(user_amount));

            if(finAmount <= 0){

                finAmount = 0;
            }
            $('#payable_amount').val(finAmount);
            
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