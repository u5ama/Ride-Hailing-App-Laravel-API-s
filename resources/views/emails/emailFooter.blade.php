@if($locale == "en")

    <tr>
        <td style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative;" align="center">
            <table class="footer" width="570" cellpadding="0" cellspacing="0"
                   role="presentation"
                   style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative;  -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0;  width: 570px;background-color:#7c3cd1">
                <tr>
                    <td class="content-cell"
                        style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative; max-width: 100vw; padding: 32px;">
                        <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative; line-height: 20pt; margin-top: 0; font-size: 14px;color: white;font-weight: 300;">
                            {{$footerTrans->emf_company_name}}, &nbsp;
                            {{$footerTrans->emf_company_address}}. &nbsp;
                            {{$footerTrans->emf_company_contacts}}
                        </p>


{{--                        <div style="padding: 0;">--}}
{{--                            <table width="100%">--}}
{{--                                <tr>--}}
{{--                        @if(isset($socialLinks))--}}
{{--                            @foreach($socialLinks as $link)--}}

{{--                                            <td width="35">--}}
{{--                                                <a href="{{$link->basl_url}}" target="_blank">--}}
{{--                                                    <!--[if mso]>--}}
{{--                                                        <table width="100%"><tr><td><img src="{{asset($link->basl_image)}}" alt="" width="30" style="width: 30px"></td></tr></table>--}}
{{--                                                <![endif]-->--}}
{{--                                                    <!--[if mso]>--}}
{{--                                                        <div style="display:none">--}}
{{--                                                <![endif]-->--}}
{{--                                                        <img src="{{asset($link->basl_image)}}" alt="" width="30px" style="max-width: 30px">--}}
{{--                                                <!--[if mso]>--}}
{{--                                                  </div>--}}
{{--                                                <![endif]-->--}}
{{--                                                    </a>--}}
{{--                                            </td>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                                </tr>--}}
{{--                            </table>--}}
{{--                        </div>--}}
                        <table cellpadding="0" cellspacing="0" class="social_icons" role="presentation"
                               style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                               valign="top" width="100%">
                            <tbody>
                            <tr style="vertical-align: top;" valign="top">
                                <td style="word-break: break-word; vertical-align: top; padding-top: 15px; padding-right: 40px; padding-bottom: 15px;"
                                    valign="top">
                                    <table align="left" cellpadding="0" cellspacing="0" class="social_table"
                                           role="presentation"
                                           style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-tspace: 0; mso-table-rspace: 0; mso-table-bspace: 0; mso-table-lspace: 0;"
                                           valign="top">
                                        <tbody>
                                        <tr align="left"
                                            style="vertical-align: top; display: inline-block; text-align: left;"
                                            valign="top">
                                            @foreach($socialLinks as $link)

                                                <td style="word-break: break-word; vertical-align: top; padding-bottom: 0; padding-right: 14px; padding-left: 0px;"
                                                    valign="top"><a href="{{ $link->basl_url }}"
                                                                    target="_blank"><img alt=""
                                                                                         height="32"
                                                                                         src="{{asset($link->basl_image)}}"
                                                                                         style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;"
                                                                                         title="Facebook"
                                                                                         width="32"/></a></td>

                                            @endforeach


                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <hr>
                        <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nu0nito Sans', sans-serif; position: relative; line-height: 20pt; margin-top: 0;  font-size: 14px;color: white;text-transform:uppercase ">
                            COPYRIGHT © 2021, ALL RIGHTS RESERVED
                        </p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>

    </table>
    </td>
    </tr>
    </table>
    </body>
    </html>

@else
    <tr>
        <td style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative;">
            <table class="footer" width="570" cellpadding="0" cellspacing="0"
                   role="presentation"
                   style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative;  -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0;  width: 570px;background-color:#7c3cd1">
                <tr>
                    <td class="content-cell"
                        style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative; max-width: 100vw; padding: 32px;">

                        <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nunito Sans', sans-serif; position: relative; line-height: 20pt; margin-top: 0; font-size: 14px;color: white;font-weight: 300;">
                            {{$footerTrans->emf_company_name}}, &nbsp;
                            {{$footerTrans->emf_company_address}}. &nbsp;
                            {{$footerTrans->emf_company_contacts}}
                        </p>

                        <table cellpadding="0" cellspacing="0" class="social_icons" role="presentation"
                               style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                               valign="top" width="100%">
                            <tbody>
                            <tr style="vertical-align: top;" valign="top">
                                <td style="word-break: break-word; vertical-align: top; padding-top: 15px; padding-right: 40px; padding-bottom: 15px;"
                                    valign="top">
                                    <table align="left" cellpadding="0" cellspacing="0" class="social_table"
                                           role="presentation"
                                           style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-tspace: 0; mso-table-rspace: 0; mso-table-bspace: 0; mso-table-lspace: 0;"
                                           valign="top">
                                        <tbody>
                                        <tr align="left"
                                            style="vertical-align: top; display: inline-block; text-align: left;"
                                            valign="top">
                                            @foreach($socialLinks as $link)

                                                <td style="word-break: break-word; vertical-align: top; padding-bottom: 0; padding-right: 14px; padding-left: 0px;"
                                                    valign="top"><a href="{{ $link->basl_url }}"
                                                                    target="_blank"><img alt=""
                                                                                         height="32"
                                                                                         src="{{asset($link->basl_image)}}"
                                                                                         style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;"
                                                                                         title="Facebook"
                                                                                         width="32"/></a></td>

                                            @endforeach


                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <hr class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 10px; padding-right: 40px; padding-bottom: 10px; padding-left: 40px;" valign="top">

                        <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Nu0nito Sans', sans-serif; position: relative; line-height: 20pt; margin-top: 0; font-size: 14px;color: white;text-transform:uppercase ">
                            حقوق الطبع والنشر © 2021 ، جميع الحقوق محفوظة
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    </table>
    </td>
    </tr>
    </table>
    </body>
    </html>



@endif
