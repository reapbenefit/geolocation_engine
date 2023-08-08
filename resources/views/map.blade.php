@extends('layouts.app')


@section('content')
    <div id="address-map-container" style="width:100%;height:400px; ">
        <div style="width: 100%; height: 100%" id="address-map"></div>
    </div>

    {{-- <form method="POST" action="{{ route('store.location', $phone ?? '0000') }}"> --}}
    <form method="POST" action="{{ secure_url('/store-location/' . $phone) }}"
        class="align-items-center d-flex justify-content-center" id="address-form">
        @csrf
        <div class="d-flex flex-column form-group w-100 mx-4">
            <label for="address_address" style="font-size: 1.2em;font-weight: bold;padding-top: 10px;">Search for an address
                below 👇 </label>
            <div class="input-group">
                <input type="text" id="address-input" name="address_address" class="form-control map-input">
                <div class="input-group-append">
                    <button class="btn btn-danger" type="button" id="clear-search-option">
                        <span style="font-size: 1em;font-weight: bold;">x</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="address_latitude" id="address-latitude" value="0" />
            <input type="hidden" name="address_longitude" id="address-longitude" value="0" />
            <input type="hidden" name="address_phone" id="address-phone" value="{{ $phone ?? '0000' }}" />
            <input type="hidden" name="address_meta_data" id="address-meta-data" value="" />
            <input type="hidden" name="flow_id" id="flow_id" value="{{ request()->input('flow_id') }}" />
            @foreach (request()->all() as $key => $value)
                @if ($key !== 'flow_id' && $key !== 'address_address')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endif
            @endforeach

            <button type="button" class="btn btn-primary mt-3" id="on-form-submit">Share by WhatsApp</button>

            <div id="loading-text" style="display: none !important;"
                class="d-flex justify-content-center align-items-center text-black accordion-body">
                <h4 class="text-center">Please wait.... 🕐 </br> we are storing your location.</h4>
            </div>

        </div>
    </form>

    <div id="address-map-container" style="width:100%;height:400px; ">
        <div style="width: 100%; height: 100%" id="mapErrorMsg"></div>
    </div>

    <form method="POST" action="{{ secure_url('/redirect-user/') }}" id="redirect-form">
        @csrf
        <input type="hidden" name="redirect_url" value="" id="redirect_url_input" />
    </form>

    @include('modals.map_loading')
@endsection
