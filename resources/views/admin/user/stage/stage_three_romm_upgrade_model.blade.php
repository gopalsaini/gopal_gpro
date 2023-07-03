
    <style>
        .fs-dropdown {
            width: 100%;
        }

    </style>
    <div class="approved-section">
        <label for="inputName">User Type : {{\App\Helpers\commonHelper::getDesignationName($user->designation_id)}}</label><br>
        <label for="inputName">User Amount : {{$user->amount}}</label>
        @php $userType = [6,4]; @endphp
       

        <div class="form-group">
            <div class="form-line">
                <label for="inputName">@lang('admin.amount') <label class="text-danger">*</label></label>
                <input type="number" class="form-control" name="base_amount" placeholder="Enter amount value" value="{{ $basePrice}}" readonly required>
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
        var early_bird = "{{$user->early_bird}}";
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