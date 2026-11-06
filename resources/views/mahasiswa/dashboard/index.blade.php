@extends('layout.userlayouts')

@section('title', 'Dashboard Mahasiswa')

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

    <!-- Halo user -->
    <div class="mb-4">
        <h2><i class="fa fa-user text-primary"></i> Dashboard Mahasiswa</h2>
       <p style="font-size: 1.05rem; font-weight: 500;">
    Selamat datang, {{ Auth::user()?->nama_user ?? 'Dosen' }}!
</p>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fa fa-calendar"></i> Jadwal Hari Ini</h5>
                    <p class="card-text display-4">{{ $jumlahJadwal }}</p>
                </div>
            </div>
        </div>
        <!-- contoh card lain, ganti angka dengan data nyata bila tersedia -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fa fa-bell"></i> Pengingat Aktif</h5>
                    <p class="card-text display-4">{{ $jumlahPengingat }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fa fa-book"></i> Mata Kuliah</h5>
                    <p class="card-text display-4">{{ $jumlahMatkul }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="mb-4">
        <h4><i class="fa fa-calendar-alt"></i> Jadwal Hari {{ $hariInText }}</h4>

        @if($jadwalHariIni->isEmpty())
            <div class="alert alert-info">Belum ada jadwal untuk hari ini.</div>
        @else
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Ruang</th>
                        <th>Dosen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $jadwal)
                        <tr class="{{ $loop->first ? 'table-info' : '' }}">
                            <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                            <td>{{ $jadwal->mataKuliah->nama_matkul ?? '-' }}</td>
                            <td>{{ $jadwal->ruang->nama_ruang ?? '-' }}</td>
                            <td>{{ $jadwal->mataKuliah->nama_dosen ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Daftar Pengingat -->
  <div class="mb-4">
    <h4><i class="fa fa-bell"></i> Pengingat Mendatang</h4>

    @if($pengingatMendatang->isEmpty())
        <p class="text-muted">Tidak ada pengingat mendatang</p>
    @else
        <ul class="list-group">
            @foreach($pengingatMendatang as $pengingat)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $pengingat->judul }}

                    <span class="badge badge-primary rounded-pill">
                        {{-- tampilkan hari jika ada --}}
                        @if($pengingat->nama_hari)
                            {{ $pengingat->nama_hari }}
                        @endif

                        {{-- tampilkan waktu jika ada --}}
                        @if($pengingat->waktu_carbon)
                            , {{ $pengingat->waktu_carbon->format('H:i') }}
                        @endif

                        {{-- jika dua-duanya kosong --}}
                        @if(!$pengingat->nama_hari && !$pengingat->waktu_carbon)
                            —
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
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
