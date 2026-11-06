@extends('layout.dosenlayouts')

@section('title', 'Pengingat')

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

    {{-- Judul dan tombol tambah --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
        <h2 class="fw-semibold"><i class="fa fa-bell text-primary mr-2"></i>Pengingat</h2>
        <a href="{{ route('pengingat.create') }}" class="btn btn-primary shadow-sm">
            <i class="fa fa-plus mr-1"></i> Tambah 
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">

  <!-- FILTER & SEARCH -->
  <form class="w-100 w-md-auto mb-2 mb-md-0" method="GET" action="{{ route('pengingat.index') }}">
    <div class="form-row align-items-center">

      <!-- Search -->
      <div class="col-12 col-sm-auto mb-2 mb-sm-0">
        <div class="input-group">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul...">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Status -->
      <div class="col-12 col-sm-auto mb-2 mb-sm-0">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value="" {{ request('status') === null || request('status')==='' ? 'selected' : '' }}>Semua</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <!-- Reset -->
      <div class="col-12 col-sm-auto">
        <a href="{{ route('pengingat.index') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
          Reset
        </a>
      </div>

    </div>
  </form>

</div>


    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- Tabel Pengingat --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
  <div class="table-responsive">
    <table class="table table-hover table-sm mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th class="text-center" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengingat as $p)
                        <tr>
                            <td>{{ $p->judul }}</td>
                            <td>{{ $p->nama_hari ?? '-' }}</td>
                            <td>{{ optional($p->waktu_carbon)->format('H:i') ?? '-' }}</td>
                            <td>
                                @if($p->is_active)
                                    <span class="badge rounded-pill bg-success">Aktif</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- Toggle Status --}}
                                <form action="{{ route('pengingat.toggle', $p->id_pengingat) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                        <i class="fa fa-power-off"></i>
                                    </button>
                                </form>

                                {{-- Edit --}}
                                <a href="{{ route('pengingat.edit', $p->id_pengingat) }}" class="btn btn-sm btn-info text-white" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('pengingat.destroy', $p->id_pengingat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengingat ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">Belum ada pengingat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-end">
        {{ $pengingat->links() }}
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