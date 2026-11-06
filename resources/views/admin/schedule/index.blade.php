{{-- resources/views/schedule/index.blade.php --}}
@extends('layout.adminlayouts')

@section('title', 'Schedule Management')

@push('head')
<style>
/* Modern Bootstrap 4 visual tweaks */
:root{
  --primary: #0d6efd;
  --muted: #6c757d;
  --card-bg: #ffffff;
  --surface: #f8fafc;
}

/* Card style */
.card {
  border: 0;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(12, 38, 63, 0.06);
  overflow: hidden;
}

/* Card header */
.card-header {
  background: linear-gradient(90deg, rgba(13,110,253,0.05), rgba(13,110,253,0.02));
  border-bottom: 1px solid rgba(0,0,0,0.03);
  font-weight: 600;
}

/* Table modern */
.table {
  border-collapse: separate;
  border-spacing: 0 10px;
}
.table thead th {
  border-bottom: none;
  color: #495057;
  font-weight: 600;
  background: transparent;
}
.table tbody tr {
  background: var(--card-bg);
  border-radius: 8px;
  box-shadow: none;
  transition: transform .08s ease, box-shadow .08s ease;
}
.table tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(12,38,63,0.06);
}
.table td, .table th {
  vertical-align: middle;
  border-top: none !important;
}

/* Buttons */
.btn-primary {
  background: var(--primary);
  border-color: rgba(13,110,253,0.9);
  box-shadow: 0 6px 16px rgba(13,110,253,0.06);
}
.btn-sm {
  padding: .35rem .6rem;
  border-radius: 8px;
}

/* Tabs */
.nav-tabs .nav-link {
  border-radius: 8px;
  color: #495057;
  margin-right: .25rem;
}
.nav-tabs .nav-link.active {
  background: var(--primary);
  color: #fff;
  font-weight: 600;
  box-shadow: 0 6px 18px rgba(13,110,253,0.08);
}

/* Avatar */
.header-avatar {
  width:44px;
  height:44px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid rgba(13,110,253,0.12);
  box-shadow: 0 6px 12px rgba(12,38,63,0.04);
}

/* Modal */
.modal-content {
  border-radius: 12px;
  overflow: hidden;
}

/* Form controls (ensure consistent look) */
.form-control, .form-select {
  border-radius: 8px;
  box-shadow: none;
  border: 1px solid rgba(0,0,0,0.08);
}

/* Footer small */
.card-footer {
  background: transparent;
  border-top: 1px solid rgba(0,0,0,0.03);
}

/* Responsive tweaks */
@media (max-width: 575.98px) {
  .table thead { display: none; }
  .table tbody td { display: block; width: 100%; }
  .table tbody tr { margin-bottom: .75rem; }
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
            <img src="{{ $avatar }}" alt="avatar" class="header-avatar">
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
<div class="container my-4">
    <h2 class="mb-3"><i class="fa fa-calendar"></i> Classroom Management</h2>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Search & per-page --}}
    <form class="mb-3 row" method="get" action="{{ route('schedule.index') }}">
        <div class="col-sm-6 col-md-4 mb-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search (ruang / gedung)">
        </div>
        <div class="col-auto mb-2">
            <select name="per" class="form-control">
                <option value="8" {{ request('per')==8?'selected':'' }}>8 / page</option>
                <option value="12" {{ request('per')==12?'selected':'' }}>12 / page</option>
                <option value="25" {{ request('per')==25?'selected':'' }}>25 / page</option>
            </select>
        </div>
        <div class="col-auto mb-2">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="scheduleTabs" role="tablist">
        @php $active = $tab ?? 'gedung'; @endphp
        
        <li class="nav-item">
            <a class="nav-link {{ $active=='gedung' ? 'active' : '' }}" href="{{ route('schedule.index', array_merge(request()->except('page'), ['tab'=>'gedung'])) }}">Gedung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $active=='ruang' ? 'active' : '' }}" href="{{ route('schedule.index', array_merge(request()->except('page'), ['tab'=>'ruang'])) }}">Ruang</a>
        </li>
    </ul>

    {{-- CONTENT --}}

  

    {{-- Gedung (with add/edit/delete) --}}
    @if($active == 'gedung')
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><strong>Gedung</strong></div>
            <div>
                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddGedung">Tambah Gedung</button>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Nama Gedung</th><th>Jumlah Ruang</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($gedungs as $g)
                    <tr>
                        <td>{{ $g->id_gedung }}</td>
                        <td>{{ $g->nama_gedung }}</td>
                        <td>{{ $g->ruangs()->count() }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-edit-gedung"
                                    data-id="{{ $g->id_gedung }}"
                                    data-nama="{{ $g->nama_gedung }}"
                                    data-toggle="modal" data-target="#modalEditGedung">
                                Edit
                            </button>

                            <form action="{{ route('gedung.destroy', $g->id_gedung) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus gedung ini? Semua ruang akan ikut terhapus jika ada.');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>Menampilkan {{ $gedungs->total() }} data</div>
            <div>{{ $gedungs->appends(request()->except('gedungs_page'))->links() }}</div>
        </div>
    </div>

    <!-- Modal Add Gedung -->
    <div class="modal fade" id="modalAddGedung" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('gedung.store') }}" method="POST" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tambah Gedung</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Gedung</label>
              <input type="text" name="nama_gedung" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button class="btn btn-primary" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Gedung -->
    <div class="modal fade" id="modalEditGedung" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form id="formEditGedung" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Edit Gedung</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id_gedung" id="editGedungId">
            <div class="form-group">
              <label>Nama Gedung</label>
              <input type="text" name="nama_gedung" id="editGedungNama" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button class="btn btn-primary" type="submit">Update</button>
          </div>
        </form>
      </div>
    </div>
    @endif

    {{-- Ruang (with add/edit/delete) --}}
    @if($active == 'ruang')
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><strong>Ruang</strong></div>
            <div>
                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddRuang">Tambah Ruang</button>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>#</th><th>Nama Ruang</th><th>Gedung</th><th>Kapasitas</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($ruangs as $r)
                    <tr>
                        <td>{{ $r->id_ruang }}</td>
                        <td>{{ $r->nama_ruang }}</td>
                        <td>{{ optional($r->gedung)->nama_gedung ?? '-' }}</td>
                        <td>{{ $r->kapasitas ?? '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-edit-ruang"
                                    data-id="{{ $r->id_ruang }}"
                                    data-nama="{{ $r->nama_ruang }}"
                                    data-gedung="{{ $r->id_gedung }}"
                                    data-kapasitas="{{ $r->kapasitas }}"
                                    data-toggle="modal" data-target="#modalEditRuang">
                                Edit
                            </button>

                            <form action="{{ route('ruang.destroy', $r->id_ruang) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ruang ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>Menampilkan {{ $ruangs->total() }} data</div>
            <div>{{ $ruangs->appends(request()->except('ruangs_page'))->links() }}</div>
        </div>
    </div>

    <!-- Modal Add Ruang -->
    <div class="modal fade" id="modalAddRuang" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form action="{{ route('ruang.store') }}" method="POST" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tambah Ruang</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Ruang</label>
              <input type="text" name="nama_ruang" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Gedung</label>
              <select name="id_gedung" class="form-control" required>
                <option value="">-- Pilih Gedung --</option>
                @foreach($gedungs as $ged)
                  <option value="{{ $ged->id_gedung }}">{{ $ged->nama_gedung }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Kapasitas</label>
              <input type="number" name="kapasitas" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button class="btn btn-primary" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Ruang -->
    <div class="modal fade" id="modalEditRuang" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form id="formEditRuang" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Edit Ruang</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editRuangId" name="id_ruang">
            <div class="form-group">
              <label>Nama Ruang</label>
              <input type="text" name="nama_ruang" id="editRuangNama" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Gedung</label>
              <select name="id_gedung" id="editRuangGedung" class="form-control" required>
                <option value="">-- Pilih Gedung --</option>
                @foreach($gedungs as $ged)
                  <option value="{{ $ged->id_gedung }}">{{ $ged->nama_gedung }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Kapasitas</label>
              <input type="number" name="kapasitas" id="editRuangKapasitas" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <button class="btn btn-primary" type="submit">Update</button>
          </div>
        </form>
      </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // base URLs (safer than hardcoding)
    const baseGedungUrl = "{{ url('gedung') }}";
    const baseRuangUrl = "{{ url('ruang') }}";

    // Gedung: populate edit modal
    document.querySelectorAll('.btn-edit-gedung').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            const idInput = document.getElementById('editGedungId');
            const namaInput = document.getElementById('editGedungNama');
            const form = document.getElementById('formEditGedung');

            if (idInput) idInput.value = id;
            if (namaInput) namaInput.value = nama;
            if (form) form.action = baseGedungUrl + '/' + id;
        });
    });

    // Ruang: populate edit modal
    document.querySelectorAll('.btn-edit-ruang').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const gedung = this.dataset.gedung;
            const kapasitas = this.dataset.kapasitas;

            const idInput = document.getElementById('editRuangId');
            const namaInput = document.getElementById('editRuangNama');
            const gedungSelect = document.getElementById('editRuangGedung');
            const kapasitasInput = document.getElementById('editRuangKapasitas');
            const form = document.getElementById('formEditRuang');

            if (idInput) idInput.value = id;
            if (namaInput) namaInput.value = nama || '';
            if (gedungSelect) gedungSelect.value = gedung || '';
            if (kapasitasInput) kapasitasInput.value = (kapasitas !== undefined && kapasitas !== null) ? kapasitas : '';
            if (form) form.action = baseRuangUrl + '/' + id;
        });
    });
});
</script>
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
      if (el) el.textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush
