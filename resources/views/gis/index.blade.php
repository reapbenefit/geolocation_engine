@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mt-4">
            <div class="d-flex justify-content-between mb-5 mt-4">
                <div class="">
                    <h4>Gis Boundries</h4>
                </div>
                <div class="">
                    <a href="{{ route('gis.create') }}" class="btn btn-success text-right">Add new boundary</a>
                    <a href="{{ route('gis.key-mappings') }}" class="btn btn-info text-right">Gis Key Mappings</a>
                </div>
            </div>

        </div>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">State</th>
                        <th scope="col">District</th>
                        <th scope="col">City</th>
                        <th scope="col">Boundary Type</th>
                        <th scope="col">Boundary Sub Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($gisBoundaries as $gisBoundary)
                        <tr>
                            <td>{{ optional($gisBoundary->state)->name }}</td>
                            <td>{{ optional($gisBoundary->district)->name }}</td>
                            <td>{{ optional($gisBoundary->city)->name }}</td>
                            <td>{{ optional($gisBoundary->boundaryType)->name }}</td>
                            <td>{{ optional($gisBoundary->boundarySubType)->name }}</td>
                            <td>{{ optional($gisBoundary)->name }}</td>
                            <td>
                                <div class="d-flex">
                                    <form action="{{ route('gis.destroy') }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $gisBoundary->id }}">
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
