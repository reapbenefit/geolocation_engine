@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 ">
                <div class="card">
                    <div class="card-header text-bold">{{ __('Add New Boundary') }}</div>

                    <div class="card-body">
                        <form action="{{ route('gis.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Select state</label>
                                <select class="form-control" id="states" name="state_id">
                                    <option value="">Select state</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Select district</label>
                                <select class="form-control" id="districts" name="district_id">
                                    <option value="">Select district</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Select city</label>
                                <select class="form-control" id="cities" name="city_id">
                                    <option value="">Select city</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Boundary Type</label>
                                <select class="form-control" id="gis_boundary_types" name="gis_boundary_type_id">
                                    <option value="">Select boundary type</option>
                                    @foreach ($boundaryTypes as $boundaryType)
                                        <option value="{{ $boundaryType->id }}">{{ $boundaryType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Boundary sub Type</label>
                                <select class="form-control" id="gis_boundary_sub_types" name="gis_boundary_sub_type_id">
                                    <option value="">Select boundary sub type</option>
                                    @foreach ($boundarySubTypes as $boundarySubType)
                                        <option value="{{ $boundarySubType->id }}">{{ $boundarySubType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group mb-3">
                                <label for="exampleInputPassword1">Description</label>
                                <input type="text" class="form-control" id="description" name="description">
                            </div>

                            <div class="form-group mb-3">
                                <label for="exampleInputPassword1">Gis file (KML file only)</label>
                                <input type="file" name="gis_file" class="form-control" id="description" accept=".kml">
                            </div>

                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
