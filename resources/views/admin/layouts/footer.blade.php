<!-- Footer opened -->
<div class="main-footer ht-40">
    <div class="container-fluid pd-t-0-f ht-100p">
        <span>{{ config('languageString.copyright') }} © {{ date("Y") }} <a href="#"> {{ config('languageString.Whipp') }}</a>. {{ config('languageString.all_right_reserved') }}.</span>
    </div>
</div>
@if(isset(auth()->guard('admin')->user()->panel_mode))
@else
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/603e1334385de407571b9905/1evp7m218';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->

@endif

<!-- Footer closed -->
