@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Welcome Passenger Email Settings</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                       aria-controls="home" aria-selected="true">Header</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                       aria-controls="profile" aria-selected="false">Body</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
                       aria-controls="contact" aria-selected="false">Footer</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" data-parsley-validate="" id="settingsFormOne" role="form">
                                @csrf
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="{{ $language->language_code }}_emh_subject">{{ $language->name }}
                                                Email Subject</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emh_subject"
                                                   id="{{ $language->language_code }}_emh_subject"
                                                   value="{{ $headerTrans->translateOrNew($language->language_code)->emh_subject }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Email Subject" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="emh_logo">Header Image</label>
                                        <input type="file" class="form-control"
                                               name="emh_logo"
                                               id="emh_logo"
                                        />
                                    </div>
                                </div>

                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="{{ $language->language_code }}_emh_title">{{ $language->name }}
                                                Header Text</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emh_title"
                                                   id="{{ $language->language_code }}_emh_title"
                                                   value="{{ $headerTrans->translateOrNew($language->language_code)->emh_title }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Header Text" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emh_description">{{ $language->name }}
                                                Header Description</label>
                                            <textarea class="form-control"
                                                      name="{{ $language->language_code }}_emh_description"
                                                      id="{{ $language->language_code }}_emh_description" cols="30"
                                                      rows="5" @if($language->is_rtl == 1)
                                                      dir="rtl"
                                                      @endif placeholder="{{$language->name}} Header Description"
                                                      required>{{ $headerTrans->translateOrNew($language->language_code)->emh_description }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" data-parsley-validate="" id="settingsFormTwo" role="form">
                                @csrf
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emb_title_text_bf_name">{{ $language->name }}
                                                Greeting Text</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emb_title_text_bf_name"
                                                   id="{{ $language->language_code }}_emb_title_text_bf_name"
                                                   value="{{ $bodyTrans->translateOrNew($language->language_code)->emb_title_text_bf_name }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Greeting Text" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emb_title_text_after_name">{{ $language->name }}
                                                Introduction Text</label>
                                            <textarea class="form-control"
                                                      name="{{ $language->language_code }}_emb_title_text_after_name"
                                                      id="{{ $language->language_code }}_emb_title_text_after_name"
                                                      cols="30" rows="5" @if($language->is_rtl == 1)
                                                      dir="rtl"
                                                      @endif placeholder="{{$language->name}} Introduction Text"
                                                      required>{{ $bodyTrans->translateOrNew($language->language_code)->emb_title_text_after_name }}</textarea>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emb_body_text_bf_button">{{ $language->name }}
                                                Button Text</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emb_body_text_bf_button"
                                                   id="{{ $language->language_code }}_emb_body_text_bf_button"
                                                   value="{{ $bodyTrans->translateOrNew($language->language_code)->emb_body_text_bf_button }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Button Text" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emb_body_text_after_button">{{ $language->name }}
                                                Body Text</label>
                                            <textarea class="form-control"
                                                      name="{{ $language->language_code }}_emb_body_text_after_button"
                                                      id="{{ $language->language_code }}_emb_body_text_after_button"
                                                      cols="30" rows="5" @if($language->is_rtl == 1)
                                                      dir="rtl" @endif placeholder="{{$language->name}} Body Text"
                                                      required>{{ $bodyTrans->translateOrNew($language->language_code)->emb_body_text_after_button }}</textarea>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" data-parsley-validate="" id="settingsFormThree" role="form">
                                @csrf
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emf_company_name">{{ $language->name }}
                                                Company Name</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emf_company_name"
                                                   id="{{ $language->language_code }}_emf_company_name"
                                                   value="{{ $footerTrans->translateOrNew($language->language_code)->emf_company_name }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Company Name" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emf_company_address">{{ $language->name }}
                                                Company Address</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emf_company_address"
                                                   id="{{ $language->language_code }}_emf_company_address"
                                                   value="{{ $footerTrans->translateOrNew($language->language_code)->emf_company_address }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Company Address" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach($languages as $language)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label
                                                for="{{ $language->language_code }}_emf_company_contacts">{{ $language->name }}
                                                Company Contact</label>
                                            <input type="text" class="form-control"
                                                   name="{{ $language->language_code }}_emf_company_contacts"
                                                   id="{{ $language->language_code }}_emf_company_contacts"
                                                   value="{{ $footerTrans->translateOrNew($language->language_code)->emf_company_contacts }}"
                                                   @if($language->is_rtl == 1)
                                                   dir="rtl"
                                                   @endif
                                                   placeholder="{{ $language->name }} Company Contact" required/>

                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->

@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/welcomeEmailSettings.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
