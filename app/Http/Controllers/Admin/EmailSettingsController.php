<?php

namespace App\Http\Controllers\Admin;

use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Language;
use App\LanguageStringTranslation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class EmailSettingsController extends Controller
{
    /**
     * Display a listing of the EmailSettings.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        return view('admin.EmailSettings.index');
    }

    /**
     * Show the form for creating a new EmailSettings.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.EmailSettings.index');
    }

    /**
     * Store a newly created EmailSettings in storage.
     *
     * @param Request $request
     * @return Factory|View
     * @throws ValidationException
     */


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
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
    }

}
