
<ul>
    @php
        $i=1;

        $stage=\App\Models\User::where('id',$userId)->first();
 
    @endphp
    
    <li>
        <a href="{{url('profile')}}" class="active">
            <span>0@php echo $i++; @endphp</span>@lang('web/profile.registered') 
        </a>
    </li>
    @if($stage->parent_id == null)
    
        <li>
            <a href="@if($stage->stage > 1) {{url('payment')}} @else # @endif" >
                <span>0@php echo $i++; @endphp</span>@lang('web/profile.payment') 
            </a>
        </li> 

    @endif
    <li>
        
        <a href="@if($stage->stage > 2) {{url('sponsorship-letter')}} @else # @endif" class="@if($stage->stage > 2) active @endif">
            <span>0@php echo $i++; @endphp</span>Sponsorship Letter
        </a>
       
    </li>
       
    <li>

        <a href="@if($stage->stage > 2) {{url('qrcode')}} @else # @endif" class="@if($stage->stage > 2) active @endif">
            <span>0@php echo $i++; @endphp</span>QR Code 
        </a>

        
    </li>
   
    
</ul>