@extends('layout.dosenlayouts')

@section('title', 'Jadwal Saya')

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
    <div>
      <h3 class="mb-0 font-weight-bold"><i class="fa fa-calendar-check text-primary mr-2"></i> Jadwal Saya</h3>
      <small class="text-muted">Kelola jadwal perkuliahan Anda dengan mudah.</small>
    </div>

    <div class="d-flex align-items-center">
     
      <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahJadwal">
        <i class="fa fa-plus mr-1"></i> Tambah
      </button>
    </div>
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

    {{-- === KONTEN JADWAL === --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Ruang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $i => $jadwal)
                        <tr>
                            <td>
                              {{ $jadwal->mataKuliah->nama_matkul ?? '-' }} <br>
                              <small class="text-muted">{{ $jadwal->mataKuliah->kode_matkul ?? '-' }}</small>
                            </td>
                            <td class="{{ $jadwal->warna }}">{{ $jadwal->hari }}</td>
                            <td>
                              <i class="fa fa-clock text-secondary"></i>
                              {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </td>
                            <td>
                              <i class="fa fa-map-marker-alt text-secondary"></i>
                              <strong>{{ $jadwal->ruang->nama_ruang ?? '-' }}</strong><br>
                              <small class="text-muted">{{ $jadwal->ruang->gedung->nama_gedung ?? '-' }}</small>
                            </td>
                            <td>
<form action="{{ route('jadwal.toggle', $jadwal->id_jadwal) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')

        @if($jadwal->is_active)
            <button class="btn btn-sm btn-secondary">
                <i class="fa fa-power-off"></i> Nonaktif
            </button>
        @else
            <button class="btn btn-sm btn-success">
                <i class="fa fa-check"></i> Aktifkan
            </button>
        @endif
    </form>

                                <!-- Tombol Edit (Bootstrap 4 attributes) -->
                                <button 
                                  type="button" 
                                  class="btn btn-sm btn-warning text-white"
                                  data-toggle="modal" 
                                  data-target="#modalEditJadwal{{ $jadwal->id_jadwal }}">
                                  <i class="fa fa-edit"></i> Edit
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('jadwal.destroy', $jadwal->id_jadwal) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Belum ada jadwal.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal tambah jadwal (Bootstrap 4 markup) --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('jadwal.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Jadwal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
              <label>Mata Kuliah</label>
              <select name="matkul" class="form-control" required>
                <option value="">Pilih Mata Kuliah</option>
                @foreach($matkuls as $m)
                  <option value="{{ $m->id_matkul }}">{{ $m->nama_matkul }}</option>
                @endforeach
              </select>
          </div>

          <div class="form-group">
              <label>Hari</label>
              <select name="hari" class="form-control" required>
                  @foreach(['senin','selasa','rabu','kamis','jumat'] as $hari)
                    <option value="{{ $hari }}">{{ ucfirst($hari) }}</option>
                  @endforeach
              </select>
          </div>

          <div class="form-row">
              <div class="form-group col-md-6">
                  <label>Jam Mulai</label>
                  <input type="time" name="jam_mulai" class="form-control" required>
              </div>
              <div class="form-group col-md-6">
                  <label>Jam Selesai</label>
                  <input type="time" name="jam_selesai" class="form-control" required>
              </div>
          </div>

          <div class="form-group">
              <label>Ruang</label>
              <select name="id_ruang" class="form-control" required>
                <option value=""> Pilih Ruang </option>
                @foreach($ruangs as $r)
                  <option value="{{ $r->id_ruang }}">{{ $r->nama_ruang }}</option>
                @endforeach
              </select>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal edit jadwal (Bootstrap 4) --}}
@foreach($jadwals as $jadwal)
<div class="modal fade" id="modalEditJadwal{{ $jadwal->id_jadwal }}" tabindex="-1" role="dialog" aria-labelledby="modalEditJadwalLabel{{ $jadwal->id_jadwal }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('jadwal.update', $jadwal->id_jadwal) }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Jadwal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
<input type="hidden" name="kelas" value="">

        <div class="modal-body">
          <div class="form-group">
              <label>Mata Kuliah</label>
              <select name="matkul" class="form-control" required>
                <option value="">Pilih Mata Kuliah</option>
                @foreach($matkuls as $m)
                  <option value="{{ $m->id_matkul }}" {{ $m->id_matkul == $jadwal->matkul ? 'selected' : '' }}>
                    {{ $m->nama_matkul }}
                  </option>
                @endforeach
              </select>
          </div>

          <div class="form-group">
              <label>Hari</label>
              <select name="hari" class="form-control" required>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari)
                  <option value="{{ $hari }}" {{ $jadwal->hari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                @endforeach
              </select>
          </div>

          <div class="form-row">
              <div class="form-group col-md-6">
                  <label>Jam Mulai</label>
                  <input type="time" name="jam_mulai" class="form-control" value="{{ $jadwal->jam_mulai }}" required>
              </div>
              <div class="form-group col-md-6">
                  <label>Jam Selesai</label>
                  <input type="time" name="jam_selesai" class="form-control" value="{{ $jadwal->jam_selesai }}" required>
              </div>
          </div>

          <div class="form-group">
              <label>Ruang</label>
              <select name="id_ruang" class="form-control" required>
                <option value="">Pilih Ruang</option>
                @foreach($ruangs as $r)
                  <option value="{{ $r->id_ruang }}" {{ $r->id_ruang == $jadwal->id_ruang ? 'selected' : '' }}>
                    {{ $r->nama_ruang }}
                  </option>
                @endforeach
              </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

@push('styles')
<style>
.nav-tabs .nav-link {
    color: #0d6efd;
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 600;
}
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
      document.getElementById('topClock').textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush