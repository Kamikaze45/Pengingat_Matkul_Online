@extends('layout.adminlayouts')

@section('title', 'Detail User')

@push('styles')
<style>
/* Tambahan styling agar lebih lega */
.card-body {
    padding: 2rem 2rem !important; /* Default 1.25rem, kita tambah jadi 2rem */
}
.table th {
    width: 170px; /* Menjaga kolom label tetap rapi */
}
</style>
@endpush

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
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Detail User</strong>
        </div>
        <div class="card-body py-5">

            <div class="d-flex align-items-center mb-3">
                {{-- Foto Profil --}}
                @if($detailUser->profile && $detailUser->profile->foto_profil)
                    <img src="{{ asset('storage/'.$detailUser->profile->foto_profil) }}" 
                         class="rounded-circle mr-3" 
                         width="70" height="70" style="object-fit: cover">
                @else
                    <i class="fa fa-user-circle fa-4x text-secondary mr-3"></i>
                @endif

                <div>
                    <h4 class="mb-0">{{ $detailUser->nama_user }}</h4>
                    <small class="text-muted">{{ $detailUser->email }}</small>
                </div>
            </div>

            <table class="table table-borderless">
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($detailUser->role) }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ ucfirst($detailUser->status) }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $detailUser->profile->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <th>No. Telepon</th>
                    <td>{{ $detailUser->profile->nomor_telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Lahir</th>
                    <td>{{ optional($detailUser->profile?->tanggal_lahir)->format('d-m-Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Bio</th>
                    <td>{{ $detailUser->profile->bio ?? '-' }}</td>
                </tr>
            </table>

            <a href="{{ route('admin.users') }}" class="btn btn-secondary mt-3">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <a href=""></a>
        </div>
    </div>
    <br><br>
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