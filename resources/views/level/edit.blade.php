@extends('layouts.template') 

@section('content') 
<div class="card card-outline card-primary"> 
    <div class="card-header"> 
        <h3 class="card-title">{{ $page->title }}</h3> 
        <div class="card-tools"></div> 
    </div> 
    <div class="card-body"> 
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible"> 
                <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5> 
                {{ session('error') }} 
            </div> 
        @endif 

        <form method="POST" action="{{ url('/level/'.$level->level_id) }}" class="form-horizontal"> 
            @csrf 
            {!! method_field('PUT') !!}  <!-- Use PUT method for updating -->
            <div class="form-group row"> 
                <label class="col-1 control-label col-form-label">Kode Level</label> 
                <div class="col-11"> 
                    <input type="text" class="form-control" id="level_kode" name="level_kode" 
                           value="{{ old('level_kode', $level->level_kode) }}" required> 
                    @error('level_kode') 
                        <small class="form-text text-danger">{{ $message }}</small> 
                    @enderror 
                </div> 
            </div> 
            <div class="form-group row"> 
                <label class="col-1 control-label col-form-label">Nama Level</label> 
                <div class="col-11"> 
                    <input type="text" class="form-control" id="level_nama" name="level_nama" 
                           value="{{ old('level_nama', $level->level_nama) }}" required> 
                    @error('level_nama') 
                        <small class="form-text text-danger">{{ $message }}</small> 
                    @enderror 
                </div> 
            </div> 
            <div class="form-group row"> 
                <label class="col-1 control-label col-form-label"></label> 
                <div class="col-11"> 
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button> 
                    <a class="btn btn-sm btn-default ml-1" href="{{ url('level') }}">Kembali</a> 
                </div> 
            </div> 
        </form> 
    </div> 
</div> 
@endsection 

@push('css') 
@endpush 

@push('js') 
@endpush 