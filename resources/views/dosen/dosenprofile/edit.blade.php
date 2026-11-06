@extends('layout.dosenlayouts')
@section('title', 'Edit Profil')

@section('content')
<!-- HEADER: (tidak diubah sama sekali) -->
<header class="bg-white shadow py-2" style="position:sticky; top:0; z-index:105;  border-bottom:3px solid #0d6efd;">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <div class="ml-3">
        <div class="text-primary" style="font-weight:600;">
          Waktu Saat Ini : <span id="topClock">--:--:--</span>
        </div>
      </div>
    </div>

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
          <a class="dropdown-item" href="{{ route('pengingat.index') }}"><i class="fa fa-bell mr-2"></i> Pengingat</a>
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
  <div class="card mx-auto shadow-lg" style="max-width:700px;">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Edit Profil</h4>
    </div>

    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Terjadi kesalahan!</strong>
          <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="form-profile">
        @csrf
        @method('PUT')

        <!-- FOTO DI ATAS -->
        <div class="text-center mb-4">
          <label class="form-label d-block">Foto Profil</label>
          <div class="mb-3">
            @if ($profile && $profile->foto_profil)
              <img id="currentAvatar" src="{{ asset('storage/' . $profile->foto_profil) }}" alt="Foto Profil" class="rounded-circle img-thumbnail" style="width:180px; height:180px; object-fit:cover;">
            @else
              <img id="currentAvatar" src="{{ asset('images/default-profile.png') }}" alt="Default Profil" class="rounded-circle img-thumbnail" style="width:180px; height:180px; object-fit:cover;">
            @endif
          </div>

          <div class="form-group w-50 mx-auto">
            <input type="file" name="foto_profil" id="foto_profil" accept="image/*" class="form-control-file">
            <small class="form-text text-muted">Pilih gambar (.jpg/.png). Kosongkan jika tidak ingin mengubah.</small>
          </div>
        </div>

        <hr>

        <!-- DATA DI BAWAH (FORM FIELDS) -->
        <div class="form-group">
          <label for="nip" class="form-label">NPM</label>
          <input type="text" name="nip" id="nip" value="{{ old('nip', $profile->nip ?? '') }}" class="form-control">
        </div>

        <div class="form-group">
          <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
          <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon', $profile->nomor_telepon ?? '') }}" class="form-control">
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}" class="form-control">
          </div>

          <div class="form-group col-md-6">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="alamat" class="form-label">Alamat</label>
          <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
        </div>

        <div class="form-group">
          <label for="bio" class="form-label">Bio</label>
          <textarea name="bio" id="bio" class="form-control" rows="3">{{ old('bio', $profile->bio ?? '') }}</textarea>
        </div>

        <!-- Media Sosial -->
        <div class="form-group">
          <label for="media_sosial" class="form-label">Media Sosial</label>
          <input type="text" name="media_sosial" id="media_sosial" value="{{ old('media_sosial', $profile->media_sosial ?? '') }}" class="form-control" placeholder="Contoh: instagram.com/indra, @indra_tw, linkedin.com/in/indra">
          <small class="form-text text-muted">
            Pisahkan beberapa akun dengan <strong>koma</strong>. Format yang didukung: URL (dgn/tdk pakai https), username Twitter (diawali <code>@</code>), atau domain/username lain.
          </small>

        </div>
        <hr>

<h5 class="mb-3 text-primary">
  <i class="fa fa-lock mr-2"></i> Ganti Password
</h5>

<div class="form-group">
  <label for="current_password" class="form-label">Password Saat Ini</label>
  <input type="password" name="current_password" id="current_password" class="form-control">
</div>

<div class="form-group">
  <label for="password" class="form-label">Password Baru</label>
  <input type="password" name="password" id="password" class="form-control">
</div>

<div class="form-group">
  <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
</div>

<small class="form-text text-muted">
  Kosongkan jika tidak ingin mengubah password.
</small>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <a href="{{ route('profile.view') }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-2"></i> Kembali</a>
          <button type="submit" class="btn btn-success"><i class="fa fa-save mr-2"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .img-thumbnail { border-radius:50%; }
  .social-btn { display:inline-block; margin-right:6px; margin-bottom:6px; }
  .card { border-radius:.75rem; }
</style>
@endpush

@push('scripts')
<script>
(function(){
  // clock
  function updateTopClock(){
    var now = new Date();
    var hh = String(now.getHours()).padStart(2,'0');
    var mm = String(now.getMinutes()).padStart(2,'0');
    var ss = String(now.getSeconds()).padStart(2,'0');
    var el = document.getElementById('topClock');
    if(el) el.textContent = hh + ':' + mm + ':' + ss;
  }
  setInterval(updateTopClock, 1000); updateTopClock();

  // Social preview logic (mirror view parsing)
  function parseSocials(raw){
    if(!raw) return [];
    var parts = raw.split(',').map(function(p){ return p.trim(); }).filter(Boolean);
    return parts;
  }

  function detectIcon(item){
    var lower = item.toLowerCase();
    if(lower.indexOf('instagram') !== -1) return 'fa-instagram';
    if(lower.indexOf('twitter') !== -1) return 'fa-twitter';
    if(lower.indexOf('facebook') !== -1) return 'fa-facebook';
    if(lower.indexOf('linkedin') !== -1) return 'fa-linkedin';
    return 'fa-link';
  }

  function makeUrl(item){
    if(item.indexOf('http') === 0) return item;
    if(item.indexOf('@') === 0) return 'https://twitter.com/' + item.replace('@','');
    return 'https://' + item;
  }

  function renderPreview(raw){
    var list = parseSocials(raw);
    var container = document.getElementById('socialPreview');
    if(!container) return;
    container.innerHTML = '';
    list.forEach(function(s){
      var icon = detectIcon(s);
      var url = makeUrl(s);
      var a = document.createElement('a');
      a.href = url;
      a.target = '_blank';
      a.className = 'btn btn-outline-secondary btn-sm rounded-circle social-btn';
      a.title = s;
      a.innerHTML = '<i class="fab ' + icon + '"></i>';
      container.appendChild(a);
    });
  }

  var input = document.getElementById('media_sosial');
  if(input){
    renderPreview(input.value);
    input.addEventListener('input', function(e){
      renderPreview(e.target.value);
    });
  }

  // preview selected avatar before upload
  var inputFile = document.getElementById('foto_profil');
  var currentAvatar = document.getElementById('currentAvatar');
  if(inputFile){
    inputFile.addEventListener('change', function(e){
      var f = e.target.files[0];
      if(!f) return;
      if(!f.type.match('image.*')) { alert('File harus gambar.'); inputFile.value=''; return; }
      var reader = new FileReader();
      reader.onload = function(ev){ if(currentAvatar) currentAvatar.src = ev.target.result; };
      reader.readAsDataURL(f);
    });
  }

})();
</script>
@endpush
