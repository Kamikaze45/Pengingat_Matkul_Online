@extends('layout.dosenlayouts')

@section('title', 'Daftar Mata Kuliah')

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
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
   <div><h3 class="font-weight-bold"><i class="fa fa-book text-primary mr-2"></i> Mata Kuliah</h3>
   <small class="text-muted">Kelola jadwal perkuliahan Anda dengan mudah.</small>
</div> 
    <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahMatkul"><i class="fa fa-plus mr-1"></i> Tambah</button>
  </div>

  {{-- === MENU TAB === --}}
  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}"
         href="{{ route('jadwal.index') }}" role="tab">
         Jadwal
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ request()->routeIs('matkul.index') ? 'active' : '' }}"
         href="{{ route('matkul.index') }}" role="tab">
         Mata Kuliah
      </a>
    </li>
  </ul>


  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
   <div class="card-body p-2 p-sm-3">
  <div class="table-responsive">
    <table class="table table-striped align-middle table-sm">

        <thead>
          <tr>
            <th>Nama Mata Kuliah</th>
            <th>Kode</th>
            <th>SKS</th>
          
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($matkuls as $i => $m)
            <tr>
              <td>{{ $m->nama_matkul }}</td>
              <td>{{ $m->kode_matkul }}</td>
              <td>{{ $m->sks }}</td>
             
              <td>
                <!-- Tombol Edit -->
                <button type="button" class="btn btn-sm btn-warning text-white"
                        data-toggle="modal"
                        data-target="#modalEditMatkul{{ $m->id_matkul }}">
                  <i class="fa fa-edit"></i> Edit
                </button>

                <!-- Tombol Hapus -->
                <form method="POST" action="{{ route('matkul.destroy', $m->id_matkul) }}" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus mata kuliah ini?')">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  </div>
</div>

{{-- Modal tambah matkul --}}
<div class="modal fade" id="modalTambahMatkul" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('matkul.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">
            <i class="fa fa-plus mr-2"></i> Tambah Mata Kuliah
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Nama Mata Kuliah</label>
            <input type="text" name="nama_matkul" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Kode</label>
            <input type="text" name="kode_matkul" class="form-control">
          </div>
          <div class="form-group">
            <label>SKS</label>
            <input type="number" name="sks" class="form-control" min="1" required>
          </div>
        
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach($matkuls as $m)
  <div class="modal fade" id="modalEditMatkul{{ $m->id_matkul }}" tabindex="-1" role="dialog" aria-labelledby="modalEditMatkulLabel{{ $m->id_matkul }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="{{ route('matkul.update', $m->id_matkul) }}">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title" id="modalEditMatkulLabel{{ $m->id_matkul }}">Edit Mata Kuliah</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="form-group">
              <label>Nama Mata Kuliah</label>
              <input type="text" name="nama_matkul" class="form-control" value="{{ old('nama_matkul', $m->nama_matkul) }}" required>
            </div>
            <div class="form-group">
              <label>Kode</label>
              <input type="text" name="kode_matkul" class="form-control" value="{{ old('kode_matkul', $m->kode_matkul) }}">
            </div>
            <div class="form-group">
              <label>SKS</label>
              <input type="number" name="sks" class="form-control" min="1" value="{{ old('sks', $m->sks) }}" required>
            </div>
          
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

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