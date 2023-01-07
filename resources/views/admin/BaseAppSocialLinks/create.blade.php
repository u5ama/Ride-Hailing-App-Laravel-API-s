@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Base APP Social Link</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Title<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="basl_title"
                                           id="basl_title"
                                           placeholder="Title" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Description<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="basl_description"
                                           id="basl_description"
                                           placeholder="Description" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">URL<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="basl_url"
                                           id="basl_url"
                                           placeholder="URL" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Link Icon<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="basl_image"
                                           id="basl_image"
                                           placeholder="Screen Info" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="basl_order_by">Order By<span class="error">*</span></label>
                                    <select class="form-control" id="basl_order_by" name="basl_order_by">
                                        @if(count($socialLinks) > 0)
                                            @foreach($socialLinks as $key=>$socialLink)
                                                @if(count($socialLinks)>0)
                                                    <option value="{{$key+1}}">{{$key+1}}</option>
                                                @else
                                                    <option value="{{1}}">{{1}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="{{1}}">{{1}}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::BaseAppSocialLink.index') }}"
                                           class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/BaseAppSocialLink.js')}}?v={{ time() }}"></script>
@endsection
