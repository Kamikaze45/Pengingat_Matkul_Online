@extends('layout.userlayouts')
@section('title', 'Detail Profil')

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

<div class="container py-5">
  <div class="row">
    <!-- Left column: profil ringkas -->
    <div class="col-12 col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          @php
            $avatar_local = ($profile && !empty($profile->foto_profil)) ? asset('storage/'.$profile->foto_profil) : asset('images/default-profile.png');
          @endphp
          <div class="position-relative d-inline-block">
            <img src="{{ $avatar_local }}" alt="Foto Profil" class="rounded-circle img-thumbnail" style="width:140px;height:140px;object-fit:cover;border:6px solid #fff; box-shadow:0 6px 18px rgba(0,0,0,0.08);">
            <span class="position-absolute" style="right:6px;bottom:6px;width:16px;height:16px;background:#28a745;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.12)"></span>
          </div>

          <h4 class="mt-3 mb-0">{{ Auth::user()?->nama_user ?? 'Pengguna' }}</h4>
          <div class="text-muted small mb-2">{{ Auth::user()?->email ?? '' }}</div>

          <div class="d-flex justify-content-center mt-3">
            <div class="text-center mr-3">
              <div class="font-weight-bold">{{ $profile->nip ?? '-' }}</div>
              <small class="text-muted">NPM</small>
            </div>
            <div class="text-center">
              <div class="font-weight-bold">{{ $profile->nomor_telepon ?? '-' }}</div>
              <small class="text-muted">Telepon</small>
            </div>
          </div>

          {{-- Sosial --}}
          @php
            $socials = collect();
            if(!empty($profile->media_sosial)){
              $parts = array_filter(array_map('trim', explode(',', $profile->media_sosial)));
              foreach($parts as $p) $socials->push($p);
            }
          @endphp

          @if($socials->isNotEmpty())
            <div class="mt-3">
              @foreach($socials as $s)
                @php
                  $lower = strtolower($s);
                  $icon = 'fa-link';
                  if(str_contains($lower,'instagram')) $icon = 'fa-instagram';
                  elseif(str_contains($lower,'twitter')) $icon = 'fa-twitter';
                  elseif(str_contains($lower,'facebook')) $icon = 'fa-facebook';
                  elseif(str_contains($lower,'linkedin')) $icon = 'fa-linkedin';
                  $url = (strpos($s,'http') === 0) ? $s : ( (str_contains($s,'@')) ? 'https://twitter.com/'.ltrim($s,'@') : 'https://'.$s );
                @endphp
                <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle mr-1" title="{{ $s }}">
                  <i class="fab {{ $icon }}"></i>
                </a>
              @endforeach
            </div>
          @endif

          <div class="mt-5">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block"><i class="fa fa-edit mr-2"></i> Edit Profil</a>
            
          </div>
        </div>
      </div>
    </div>

    <!-- Right column: detail -->
    <div class="col-12 col-lg-8 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Detail Informasi</h5>
          <small class="text-muted">Terakhir diubah: {{ optional($profile)->updated_at?->format('d M Y H:i') ?? '-' }}</small>
        </div>

        <div class="card-body">
          <div class="row">

            <div class="col-md-6 mb-3">
              <label class="font-weight-bold">Email</label>
              <div class="form-control-plaintext">{{Auth::user()?->email ?? '-' }}</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="font-weight-bold">Tanggal Lahir</label>
             <div class="form-control-plaintext">
  {{ $profile?->tanggal_lahir ? $profile->tanggal_lahir->format('d-m-Y') : '-' }}
</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="font-weight-bold">Jenis Kelamin</label>
              <div class="form-control-plaintext">
                @if(($profile->jenis_kelamin ?? '') == 'L') Laki-laki
                @elseif(($profile->jenis_kelamin ?? '') == 'P') Perempuan
                @else -
                @endif
              </div>

            </div>
            <div class="col-6 mb-3">
              <label class="font-weight-bold">Alamat</label>
              <div class="form-control-plaintext">{{ $profile->alamat ?? '-' }}</div>
            </div>
            
            <div class="col-12 mb-3">
              <label class="font-weight-bold">Bio</label>
              <div class="p-3 bg-light rounded">{{ $profile->bio ?? '-' }}</div>
            </div>
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Ingin memperbarui data lainnya? Klik tombol edit.</small>
            <a href="{{ route('profile.edit') }}" class="btn btn-success"><i class="fa fa-pen mr-2"></i> Edit Profil</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@push('styles')
<style>
  /* Simple modern tweaks compatible with Bootstrap 4 */
  .card { border-radius: .75rem; }
  .form-control-plaintext { padding:.375rem .75rem; background:transparent; }
</style>
@endpush

@push('scripts')
<script>
  (function(){
    function updateTopClock(){
      var now = new Date();
      var hh = String(now.getHours()).padStart(2,'0');
      var mm = String(now.getMinutes()).padStart(2,'0');
      var ss = String(now.getSeconds()).padStart(2,'0');
      var el = document.getElementById('topClock');
      if(el) el.textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush
