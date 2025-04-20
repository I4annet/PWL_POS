@extends('layouts.template')

@section('content')
<div class="container mt-5">
    <div class="card shadow rounded-lg p-4">
        <div class="d-flex flex-column align-items-center text-center">
            <div class="position-relative">
                <img 
                src="{{ Auth::user()->image ? asset('storage/profile/' . Auth::user()->image) : asset('storage/profile/default-user.png') }}" 
                alt="Profile Picture"
                class="rounded-circle"
                width="150"
                height="150"
                style="object-fit: cover;"
              >
                <button id="uploadPhotoBtn" class="btn btn-sm btn-primary position-absolute" style="bottom: 0; right: 0;">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h4 class="mt-3">{{ Auth::user()->nama }}</h4>
            <p class="text-muted mb-1">{{ Auth::user()->username }}</p>
            <p class="text-muted">Role: {{ Auth::user()->getRoleName() }}</p>
        </div>

        <hr>

        <div id="uploadPhotoModal" class="mt-3" style="display:none;">
            <form id="uploadPhotoForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="image">Pilih Foto Baru</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required />
                </div>
                <button type="submit" class="btn btn-success btn-sm">Upload</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('uploadPhotoModal').style.display = 'none';">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('uploadPhotoBtn').onclick = function() {
        document.getElementById('uploadPhotoModal').style.display = 'block';
    };

    document.getElementById('uploadPhotoForm').onsubmit = function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('/profile/import_ajax', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Gagal upload foto');
            }
        });
    };
</script>
@endsection
