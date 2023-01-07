<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppSocialLinks;
use App\CustomerCreditCard;
use App\EmailBody;
use App\EmailBodyTranslation;
use App\EmailFooter;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Language;
use App\LanguageStringTranslation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class PassengerVerifyEmailSettingsController extends Controller
{
    /**
     * Display a listing of the PassengerVerifyEmailSettings.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

        }
        $languages = Language::where('status', 1)->get();
        $header = EmailHeader::where('id', 10)->first();
        $headerTrans = EmailHeader::listsTranslations('name')->select('email_headers.id', 'email_headers.emh_logo', 'email_header_translations.emh_subject', 'email_header_translations.emh_title', 'email_header_translations.emh_description')->where('email_headers.id', 10)->first();
        $bodyTrans = EmailBody::listsTranslations('name')->select('email_bodies.id', 'email_body_translations.emb_title_text_bf_name', 'email_body_translations.emb_title_text_after_name', 'email_body_translations.emb_body_text_bf_button', 'email_body_translations.emb_body_text_after_button')->where('email_bodies.id', 10)->first();
        $footerTrans = EmailFooter::listsTranslations('name')->select('email_footers.id', 'email_footer_translations.emf_company_name', 'email_footer_translations.emf_company_address', 'email_footer_translations.emf_company_contacts')->where('email_footers.id', 10)->first();
        return view('admin.PassengerVerifyEmailSettings.index', ['languages' => $languages, 'header' => $header, 'headerTrans' => $headerTrans, 'bodyTrans' => $bodyTrans, 'footerTrans' => $footerTrans]);
    }

    /**
     * Show the form for creating a new PassengerVerifyEmailSettings.
     *
     * @return Factory|View
     */
    public function create()
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.PassengerVerifyEmailSettings.create', ['languages' => $languages]);
    }

    /**
     * Store a newly created PassengerVerifyEmailSettings in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function FormOneCreate(Request $request)
    {
        if ($request->hasFile('emh_logo')) {
            $mime = $request->emh_logo->getMimeType();
            $logo = $request->file('emh_logo');
            $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
            $logoName = time() . '-' . $logo_name;
            $logo->move('./assets/PassengerVerifyEmail/logo/', $logoName);
            $welcomeEmailLogo = 'assets/PassengerVerifyEmail/logo/' . $logoName;
        }else{
            $welcome = EmailHeader::where('id',10)->first();
            $welcomeEmailLogo = $welcome->emh_logo;
        }

        $head = EmailHeader::updateOrCreate([
            'id' => 10,
        ], [
            'emh_logo' => $welcomeEmailLogo,
        ]);

        $languages = Language::where('status', 1)->get();
        foreach ($languages as $language) {
            EmailHeaderTranslation::updateOrCreate([
                'email_header_id' => $head->id,
                'locale' => $language->language_code,
            ], [
                'emh_subject' => $request->input($language->language_code . '_emh_subject'),
                'emh_title' => $request->input($language->language_code . '_emh_title'),
                'emh_description' => $request->input($language->language_code . '_emh_description'),
                'locale' => $language->language_code,
            ]);
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.email_header_inserted')]);
    }

    /**
     * Store a newly created PassengerVerifyEmailSettings in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function FormTwoCreate(Request $request)
    {
        $body = EmailBody::updateOrCreate([
            'id' => 10,
        ], [
            'logo' => '',
        ]);

        $languages = Language::where('status', 1)->get();
        foreach ($languages as $language) {
            EmailBodyTranslation::updateOrCreate([
                'email_body_id' => $body->id,
                'locale' => $language->language_code,
            ], [
                'emb_title_text_bf_name' => $request->input($language->language_code . '_emb_title_text_bf_name'),
                'emb_title_text_after_name' => $request->input($language->language_code . '_emb_title_text_after_name'),
                'emb_body_text_after_button' => $request->input($language->language_code . '_emb_body_text_after_button'),
                'locale' => $language->language_code,
            ]);
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.email_body_inserted')]);
    }

    /**
     * Store a newly created PassengerVerifyEmailSettings in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function FormThreeCreate(Request $request)
    {
        $body = EmailFooter::updateOrCreate([
            'id' => 10,
        ], [
            'logo' => '',
        ]);

        $languages = Language::where('status', 1)->get();
        foreach ($languages as $language) {
            EmailFooterTranslation::updateOrCreate([
                'email_footer_id' => $body->id,
                'locale' => $language->language_code,
            ], [
                'emf_company_name' => $request->input($language->language_code . '_emf_company_name'),
                'emf_company_address' => $request->input($language->language_code . '_emf_company_address'),
                'emf_company_contacts' => $request->input($language->language_code . '_emf_company_contacts'),
                'locale' => $language->language_code,
            ]);
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.email_footer_inserted')]);
    }


    /**
     * Display the specified PassengerVerifyEmailSettings.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $languages = Language::where('status', $id)->get();
        $header = EmailHeader::where('id', $id)->first();
        $headerTrans = EmailHeaderTranslation::where(['email_header_id' => $id, 'locale' => 'en'])->first();
        $bodyTrans = EmailBodyTranslation::where(['email_body_id' => $id, 'locale' => 'en'])->first();
        $footerTrans = EmailFooterTranslation::where(['email_footer_id' => $id, 'locale' => 'en'])->first();
        $socialLinks = BaseAppSocialLinks::all();

        return view('admin.PassengerVerifyEmailSettings.show', ['languages' => $languages, 'header' => $header, 'headerTrans' => $headerTrans,
            'bodyTrans' => $bodyTrans, 'footerTrans' => $footerTrans, 'socialLinks' => $socialLinks]);
    }

    /**
     * Show the form for editing the specified PassengerVerifyEmailSettings.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

}
