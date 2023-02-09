
<ul>
    @php
        $i=1;

        $SpouseInfoResult=\App\Models\User::where('id',$userId)->where('added_as','Spouse')->first();
        $stage=\App\Models\User::where('id',$userId)->first();
 
    @endphp
    @if($groupInfoResult) 
        <li>
            <a href="{{url('groupinfo-update')}}" class="active">
                <span>@php echo $i++; @endphp</span>@lang('web/profile.group') @lang('web/profile.info')
            </a>
        </li>
    @endif
    
    <li>
        <a href="{{url('profile')}}" class="active">
            <span>0@php echo $i++; @endphp</span>@lang('web/profile.registered') 
        </a>
    </li>
   @if(!$SpouseInfoResult)
        
        <!-- changes by Gopal -->
        <li>
            <a href="@if($stage->stage > 1) {{url('payment')}} @else # @endif" >
            <!-- <a href="@if($stage->stage > 1) # @else # @endif" > -->
                <span>0@php echo $i++; @endphp</span>@lang('web/profile.payment') 
            </a>
        </li> 
    
        <li>
            <a href="@if($stage->stage > 2) # @else # @endif" class="@if($stage->stage > 2) active @endif">
            <!-- <a href="@if($stage->stage > 2) {{url('travel-information')}} @else # @endif" class="@if($stage->stage > 2) active @endif"> -->
                <span>0@php echo $i++; @endphp</span>@lang('web/profile.travel') @lang('web/profile.info')
            </a>
        </li>
       
    @endif
   
    <li>
        <a href="@if($stage->stage > 3) {{url('session-information')}} @else # @endif" class="@if($stage->stage > 3) active @endif">
            <span>0@php echo $i++; @endphp</span>@lang('web/profile.session') 
        </a>
    </li>
   
    
    <li>
        <a href="@if($stage->stage > 4) {{url('event-day-information')}} @else # @endif" class="@if($stage->stage > 4) active @endif">
            <span>0@php echo $i++; @endphp</span> @lang('web/profile.event-day')
        </a>
    </li>
    
</ul>