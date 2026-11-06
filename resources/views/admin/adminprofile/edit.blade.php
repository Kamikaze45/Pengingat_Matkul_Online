
@extends('layout.adminlayouts')
@section('title', 'Edit Profil')

@section('content')
<!-- HEADER: paste langsung di atas <div class="container mt-4"> -->
<header class="bg-white shadow py-2" style="position:sticky; top:0; z-index:105;  border-bottom:3px solid #0d6efd;">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <!-- left: logo + app name + waktu -->
    <div class="d-flex align-items-center gap-3">
      

      <!-- waktu (live clock) -->
      <div class="ml-3">
        <div class="text-primary" style="font-weight:600;">
          Waktu Saat Ini : <span id="topClock">--:--:--</span>
</div>
      </div>
    </div>

    <!-- right: name + avatar + dropdown -->
    <div class="d-flex align-items-center">
      <div class="d-none d-sm-block text-right mr-3">
        <div style="font-weight:700;color:#0b5ed7;">{{ strtoupper(Auth::user()?->nama_user ?? 'USER') }}</div>
      </div>

      <div class="dropdown">
        @php
          $profile = Auth::user()->profile ?? null;
          $avatar = ($profile && !empty($profile->foto_profil)) ? asset('storage/'.$profile->foto_profil) : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
        @endphp

        <a class="d-flex align-items-center text-decoration-none" href="#" id="headerProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div style="position:relative;">
            <img src="{{ $avatar }}" alt="avatar" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid rgba(13,110,253,0.12);">
            <span style="position:absolute; right:-2px; bottom:-2px; width:12px; height:12px; background:#28a745; border-radius:50%; border:2px solid #fff; box-shadow:0 0 0 2px rgba(0,0,0,0.04)"></span>
          </div>
        </a>

        <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="headerProfile" style="min-width:200px;">
          <div class="px-3 py-2">
            <strong>{{ Auth::user()?->nama_user ?? 'Pengguna' }}</strong>
            <div class="small text-muted">{{ Auth::user()?->email ?? '' }}</div>
          </div>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="{{ route('profile.view') }}"><i class="fa fa-user mr-2"></i> Profil saya</a>
          <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
            <i class="fa fa-sign-out-alt mr-2"></i> Logout
          </a>
          <form id="logout-form-top" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- /HEADER -->
<div class="container py-5">
    <div class="card shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Profil</h4>
        </div>
        <div class="card-body">
            {{-- Pesan sukses --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pesan error validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form edit profil --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Foto Profil --}}
                <div class="mb-4 text-center">
                    <label for="foto_profil" class="form-label d-block">Foto Profil</label>
                    <div class="mb-3">
                        @if ($profile && $profile->foto_profil)
                            <img src="{{ asset('storage/' . $profile->foto_profil) }}"
                                 alt="Foto Profil"
                                 class="rounded-circle me-2"
                                 width="150" height="150">
                        @else
                             <span class="mr-2"><i class="fa fa-user-circle fa-2x text-secondary"></i></span>
                        @endif
                    </div>
                    <input type="file" name="foto_profil" id="foto_profil" class="form-control w-50 mx-auto">
                </div>

                <hr>

                {{-- Alamat --}}
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
                </div>

                {{-- Nomor Telepon --}}
                <div class="mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon"
                           value="{{ old('nomor_telepon', $profile->nomor_telepon ?? '') }}"
                           class="form-control">
                </div>

                {{-- Tanggal Lahir --}}
                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}"
                           class="form-control">
                </div>

                {{-- Jenis Kelamin --}}
                <div class="mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
    <option value="">-- Pilih --</option>
    <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
    <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
</select>

                </div>

                {{-- Bio --}}
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="3">{{ old('bio', $profile->bio ?? '') }}</textarea>
                </div>

                {{-- Media Sosial --}}
                <div class="mb-3">
                    <label for="media_sosial" class="form-label">Media Sosial</label>
                    <input type="text" name="media_sosial" id="media_sosial"
                           value="{{ old('media_sosial', $profile->media_sosial ?? '') }}"
                           class="form-control" placeholder="Instagram, LinkedIn, dll">
                </div>

                {{-- Tombol Simpan --}}
               <div class="text-end d-flex justify-content-between">
    <a href="{{ route('profile.view') }}" class="btn btn-secondary px-4">
        <i class="fa fa-arrow-left me-2"></i> Kembali
    </a>

    <button type="submit" class="btn btn-success px-4">
        <i class="fa fa-save me-2"></i> Simpan Perubahan
    </button>
</div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    function updateTopClock(){
      var now = new Date();
      var hh = String(now.getHours()).padStart(2,'0');
      var mm = String(now.getMinutes()).padStart(2,'0');
      var ss = String(now.getSeconds()).padStart(2,'0');
      document.getElementById('topClock').textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush