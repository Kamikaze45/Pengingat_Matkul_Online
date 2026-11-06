@extends('layout.adminlayouts')

@section('title', 'Dashboard')

@push('styles')
<!-- Styles khusus untuk mencegah "goyang" / resize chart -->
<style>
/* Pastikan scrollbar vertikal selalu ada untuk menghindari content shift */
html { overflow-y: scroll; }

/* Atur tinggi chart agar tidak ikut resize otomatis */
.chart {
    max-height: 350px;
    height: 350px !important;
    display: block;
}

/* Untuk card-body agar canvas tidak saling dorong */
.card-body.d-flex-chart {
    display: flex !important;
    flex-direction: column;
    align-items: stretch;
    justify-content: center;
    padding: 1rem;
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
<div class="d-flex mt-4 justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
    <h1 class="h2"><i class="fa fa-home"></i> Dashboard</h1>
   
</div>

<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-header"><i class="fa fa-user-plus"></i> User Registrations</div>
            <div class="card-body">
                <h3 class="card-title">{{ $userRegistrations ?? 44 }}</h3>
            </div>
            <a class="card-footer text-right text-white" href="{{ route('admin.users') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
        <div class="card text-white bg-info">
            <div class="card-header"><i class="fa fa-user-plus"></i> Dosen Registrations</div>
            <div class="card-body">
                <h3 class="card-title">{{ $dosenRegistrations ?? 44 }}</h3>
            </div>
            <a class="card-footer text-right text-white" href="{{ route('admin.users') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
        <div class="card text-white bg-success">
            <div class="card-header"><i class="fa fa-user-plus"></i> Admin Registrations</div>
            <div class="card-body">
                <h3 class="card-title">{{ $adminRegistrations ?? 44 }}</h3>
            </div>
            <a class="card-footer text-right text-white" href="{{ route('admin.users') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-header"><i class="fa fa-user-plus"></i> Request Akun</div>
            <div class="card-body">
                <h3 class="card-title">{{ $approvedRegistrations ?? 65 }}</h3>
            </div>
            <a class="card-footer text-right text-white" href="{{ route('admin.users') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
        <div class="card-collapsible card">
            <div class="card-header">Doughnut Chart</div>
            <div class="card-body d-flex flex-column">
    <canvas class="chart w-100" id="pieChart"></canvas>
</div>

        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
        <div class="card-collapsible card">
            <div class="card-header">Bar Chart</div>
            <div class="card-body d-flex-chart">
                <canvas class="chart w-100" id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Ambil data dari variabel PHP
const userRegistrations = <?= json_encode($userRegistrations) ?> || [];
const dosenRegistrations = <?= json_encode($dosenRegistrations) ?> || [];
const adminRegistrations = <?= json_encode($adminRegistrations) ?> || [];

// Hitung total untuk persentase
const totalRegistrations = userRegistrations + dosenRegistrations + adminRegistrations;

// Data untuk doughnut chart
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['User', 'Dosen', 'Admin'],
        datasets: [{
            data: [userRegistrations, dosenRegistrations, adminRegistrations],
            backgroundColor: [
                '#ffc107', // Warning color for User
                '#17a2b8', // Info color for Dosen
                '#28a745'  // Success color for Admin
            ]
        }]
    },
    options: {
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var total = dataset.data.reduce(function(previousValue, currentValue) {
                        return previousValue + currentValue;
                    });
                    var currentValue = dataset.data[tooltipItem.index];
                    var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                    return data.labels[tooltipItem.index] + ': ' + percentage + '%';
                }
            }
        }
    }
});

const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Jumlah Pengunjung',
            data: <?= json_encode($visitorsData) ?>,
            backgroundColor: '#007bff'
        }]
    },
    options: {
        responsive: true,
        scales: {
            yAxes: [{
                ticks: { beginAtZero:true }
            }]
        }
    }
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
      document.getElementById('topClock').textContent = hh + ':' + mm + ':' + ss;
    }
    setInterval(updateTopClock, 1000);
    updateTopClock();
  })();
</script>
@endpush