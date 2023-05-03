
<style>
        .fs-dropdown {
            width: 100%;
        }

    </style>
    <div class="approved-section">
        
        <div class="form-group">
            <div class="form-line">
                <label for="inputName">Payable Amount <label class="text-danger">*</label></label>
                <input type="number" class="form-control" name="amount" id="payable_amount" value="{{$basePrice}}" readonly required>
            </div>
        </div>
    </div>

    <script>

    $(document).ready(function() {
        var payable_amount = $('#payable_amount').val();
        var base_amount = "{{$basePrice}}";
        var finAmount = payable_amount;
         
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