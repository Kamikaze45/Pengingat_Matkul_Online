@extends('layout.userlayouts')

@section('title', 'Edit Pengingat')

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
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <strong>Edit Pengingat</strong>
        </div>

        <div class="card-body">
            <form action="{{ route('pengingat.update', $p->id_pengingat) }}" method="POST">
                @csrf
               

                <div class="form-group mb-2">
                    <label>Judul </label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $p->judul) }}" required>
                </div>

                <div class="form-group mb-2">
                    <label>Pesan</label>
                    <textarea name="pesan" rows="3" class="form-control">{{ old('pesan', $p->pesan) }}</textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Hari </label>
                    <select name="hari" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                            <option value="{{ $hari }}" {{ old('hari', $p->hari) == $hari ? 'selected' : '' }}>
                                {{ $hari }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Waktu </label>
                    <input type="time" name="waktu" class="form-control"
                        value="{{ old('waktu', isset($p->waktu) ? substr($p->waktu, 0, 5) : '') }}" required>
                </div>

                <div class="form-group form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" class="form-check-input" id="activeCheck"
                        value="1" {{ old('is_active', $p->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeCheck">Aktifkan</label>
                </div>

                <button class="btn btn-success"><i class="fa fa-save"></i> Update</button>
                <a href="{{ route('pengingat.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
