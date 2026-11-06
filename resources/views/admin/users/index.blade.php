@extends('layout.adminlayouts')

@section('title', 'User Management')

@push('styles')
<style>
/* Modernized styles (Bootstrap 4) */
.card-user-table {
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 10px 30px rgba(12,38,63,0.06);
    border: 1px solid rgba(13,110,253,0.04);
}

/* Table */
.table {
    border-collapse: separate;
    border-spacing: 0 10px;
}
.table thead th {
    background: transparent;
    border-bottom: 0;
    font-weight: 600;
    color: #495057;
}
.table tbody tr {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 6px 14px rgba(12,38,63,0.04);
    transition: transform .08s ease, box-shadow .08s ease;
}
.table tbody tr:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(12,38,63,0.06);
}
.table td, .table th {
    vertical-align: middle;
    border-top: none !important;
}


/* Badges (BS4 friendly) */
.badge-status {
    font-size: .8rem;
    padding: .35em .6em;
    border-radius: 999px;
    display:inline-block;
}

/* Buttons */
.btn-sm {
    padding: .35rem .6rem;
    border-radius: 8px;
}
.action-btn-group .dropdown-menu { min-width: 160px; }

/* Helpers */
.table-responsive { overflow: visible; }
@media (max-width: 575.98px) {
  .table thead { display: none; }
  .table tbody td { display: block; width: 100%; }
  .table tbody tr { margin-bottom: .75rem; }
}
/* pastikan avatar-sm selalu 40x40 dan tidak ter-override */


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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2"><i class="fa fa-users"></i> User Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card card-user-table mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users') }}" class="form-row align-items-center mb-3">
                <div class="col-auto">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or email...">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-control">
                        <option value="all" {{ request('status','all')=='all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary">Filter</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-link">Reset</a>
                </div>
                <div class="col text-right">
                    <small class="text-muted">Total: {{ $users->total() }} user</small>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="width:140px">Status</th>
                            <th style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
  @if($user->profile && $user->profile->foto_profil)
   
    <img src="{{ asset('storage/' . $user->profile->foto_profil) }}" class="avatar-sp mr-3">


  @else
    <span class="mr-2"><i class="fa fa-user-circle fa-2x text-secondary"></i></span>
  @endif

  {{ $user->nama_user }}
</td>

                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if($user->status == 'pending')
                                    <span class="badge badge-warning badge-status text-dark">Pending</span>
                                @elseif($user->status == 'approved')
                                    <span class="badge badge-success badge-status">Approved</span>
                                @elseif($user->status == 'admin')
                                    <span class="badge badge-primary badge-status">Admin</span>
                                @elseif($user->status == 'dosen')
                                    <span class="badge badge-info badge-status text-white">Dosen</span>
                                @else
                                    <span class="badge badge-secondary badge-status">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group action-btn-group" role="group">
                                    {{-- Approve button (disable if already approved) --}}
                                    <form action="{{ route('admin.users.approve', $user->id_user) }}" method="POST" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" {{ $user->status == 'approved' ? 'disabled' : '' }}
                                            onclick="return confirm('Setujui user {{ $user->nama_user }}?')">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>

                                    {{-- Set to Pending (only when approved) --}}
                                    @if($user->status == 'approved')
                                    <form action="{{ route('admin.users.pending', $user->id_user) }}" method="POST" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" 
                                            onclick="return confirm('Kembalikan status {{ $user->nama_user }} menjadi pending?')"
                                            title="Set to pending">
                                            <i class="fa fa-hourglass-half"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Dropdown for other actions --}}
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('admin.users') }}?view={{ $user->id_user }}"><i class="fa fa-eye mr-1"></i> View</a>

                                        <div class="dropdown-divider"></div>

                                        <form action="{{ route('admin.users.delete', $user->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama_user }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit"><i class="fa fa-trash mr-1"></i> Delete</button>
                                        </form>

                                        <form action="{{ route('admin.users.resetPassword', $user->id_user) }}" method="POST" onsubmit="return confirm('Reset password user {{ $user->nama_user }} ke default?')">
                                            @csrf
                                            <button class="dropdown-item" type="submit"><i class="fa fa-key mr-1"></i> Reset Password</button>
                                        </form>
                                      </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Tidak ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}</small>
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>

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
      var el = document.getElementById('topClock');
      if (el) el.textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush
