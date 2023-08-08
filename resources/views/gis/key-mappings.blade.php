@extends('layouts.app')

@section('content')
    <div class="container" id="gis-key-container">
        <div class="mt-4">
            <div class="d-flex justify-content-between mb-5 mt-4">
                <div class="">
                    <h4>Gis Key Mappings</h4>
                </div>
                <div class="">
                    <button data-bs-toggle="modal" data-bs-target="#createGisKeyModal" class="btn btn-success text-right">Add
                        New
                        Key</a>
                </div>
            </div>
            @if ($errors->any())
                <h4 class="error_message text-danger">{{ $errors->first() }}</h4>
            @endif
        </div>

        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Gis File Key</th>
                        <th scope="col">Mapped into <br> (This will show on glific/other platforms)</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($gisKeysMappings as $gisKeysMapping)
                        <tr>
                            <td>{{ optional($gisKeysMapping)->gis_key }}</td>
                            <td>{{ optional($gisKeysMapping->gisBoundarySubType)->slug }}</td>
                            <td>
                                <div class="d-flex">
                                    <form action="{{ route('gis.destroy-key-mappings') }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $gisKeysMapping->id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm btn-light">Delete</button>
                                    </form>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection


<div>
    <div class="modal fade" id="createGisKeyModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="{{ route('gis.create-key-mappings') }}" method="POST">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Key</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        @csrf
                        <div class="form-group mb-3">
                            <label for="gisKey">Gis Key</label>
                            <input type="text" class="form-control" id="gisKey" name="gis_key" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="gisBoundarySubTypeId">Select Mapping</label>
                            <select class="form-control" id="gisBoundarySubTypeId" name="gis_boundary_sub_type_id"
                                required>
                                <option value="">Select Mapping</option>
                                @foreach ($subTypes as $subType)
                                    <option value="{{ $subType->id }}">{{ $subType->slug }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save key</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
