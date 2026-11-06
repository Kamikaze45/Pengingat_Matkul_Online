<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>@yield('title','Dashboard') - {{ config('app.name','Laravel') }}</title>
  <link rel="icon" href="data:,">
  <meta name="language" content="en-EN" />
  <meta name="keywords" content="bootstrap, bootstrap 4, bootstrap 4 template, bootstrap 4 admin, bootstrap 4 dashboard" />
  <meta name="description" content="@yield('description','Admin dashboard')" />

  <!-- Bootstrap 4 & Font Awesome (match contoh kedua) -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


  <style>
    :root{
      --sidebar-width: 230px;
      --sidebar-bg: #f8f9fa;
      --primary: #0d6efd;
      --overlay-bg: rgba(0,0,0,0.45);
    }
    html,body{height:100%;}

    /* Sidebar base */
    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: var(--sidebar-width);
      height: 100vh;
      background: var(--sidebar-bg);
      border-right: 1px solid rgba(0,0,0,.08);
      padding-top: 25px;
      overflow-y: auto;
      transition: transform .22s ease;
      z-index: 1040;
      transform: translateX(0);
    }

    .sidebar .app-title { font-weight:700; font-size:1.25rem; padding:0 1.2rem; margin-bottom:22px; color:#222; display:flex; align-items:center; gap:10px; }
    .sidebar .nav-link{ font-size:.95rem; padding:.75rem 1.2rem; color:#444; display:flex; align-items:center; gap:12px; border-radius:6px; margin:6px 10px; transition:.12s ease; }
    .sidebar .nav-link i{ width:22px; text-align:center; }
    .sidebar .nav-link:hover{ background:rgba(0,0,0,0.05); color:var(--primary); text-decoration:none; }
    .sidebar .nav-link.active{ background:#e9f0ff; color:var(--primary); font-weight:600; }
    .sidebar .sidebar-footer{ position:absolute; bottom:1rem; left:1rem; right:1rem; font-size:.85rem; color:#6c757d; }

    /* Overlay */
    .sidebar-overlay { position: fixed; inset:0; background: var(--overlay-bg); z-index: 1035; opacity:0; visibility:hidden; transition: opacity .18s ease, visibility .18s ease; }
    .sidebar-overlay.show { opacity:1; visibility:visible; }

    .avatar-sp {
    width: 40px !important;
    height: 40px !important;
    object-fit: cover !important;
    border-radius: 50% !important;
    display: inline-block !important;
    vertical-align: middle !important;
}


    /* Toggle (burger) */
    .btn-sidebar-toggle {
      position: fixed; top: 12px; left: 12px; z-index: 1060;
      display: none; border-radius:8px; padding:.5rem .6rem;
      background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.06);
    }
    .btn-sidebar-toggle:focus { outline: 3px solid rgba(13,110,253,0.12); }

    /* Layout main */
    .main-content { margin-left: var(--sidebar-width); padding: 28px; min-height: 100vh; background: #fff; transition: margin-left .18s ease; }

    @media (max-width: 991.98px) {
      .btn-sidebar-toggle { display:inline-block; padding:.6rem .7rem; border-radius:10px; }
      .main-content { margin-left: 0; padding-top: 72px; }
    }

    @media (min-width: 992px) {
      .btn-sidebar-toggle { display:none !important; }
      .main-content { margin-left: var(--sidebar-width); padding-top: 28px; }
    }

    /* Keep footer visible above other elements if needed */
    footer.footer { position: relative; z-index: 1050; }

    /* Optional small helpers to reproduce previous pagination fixes if main.css not present */
    .pagination { display: inline-flex !important; flex-wrap: nowrap !important; gap: .25rem !important; align-items: center !important; margin: 0 !important; padding: 0 !important; list-style: none !important; background: transparent !important; width: auto !important; }
    .page-link { display: inline-flex !important; align-items: center !important; justify-content: center !important; padding: .375rem .65rem !important; min-width: 36px !important; height: 36px !important; font-size: .95rem !important; line-height: 1 !important; border-radius: .25rem !important; box-sizing: border-box !important; }
  </style>

  @stack('head')
</head>
<body>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar" role="navigation" aria-label="Sidebar navigation" aria-hidden="false">
    <div class="app-title">
      <i class="fa fa-graduation-cap"></i>
      {{ config('app.name','E-Remind') }}
    </div>

    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('dashboard*')) active @endif" href="{{ route('dashboard') }}">
          <i class="fa fa-home"></i> Dashboard
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('schedule.*') || request()->routeIs('gedung.*')) active @endif" href="{{ route('schedule.index') }}">
          <i class="fa fa-calendar"></i> Classroom Management
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('profile.*')) active @endif" href="{{ route('profile.view') }}">
          <i class="fa fa-user"></i> Profile
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users') }}">
          <i class="fa fa-users"></i> User Management
        </a>
      </li>

     <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.reset.list') }}">
      <i class="fa fa-lock"></i> Reset Request
    </a>
</li>


      <li class="nav-item">
        @auth
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fa fa-power-off"></i> Logout
        </a>
        @endauth
      </li>
    </ul>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    <div class="sidebar-footer text-center">
      <small>© {{ date('Y') }} {{ config('app.name') }}</small>
    </div>
  </aside>

  <!-- Overlay -->
  <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

  <!-- Toggle button -->
  <button class="btn btn-light btn-sidebar-toggle" id="btnSidebarToggle" aria-controls="sidebar" aria-expanded="false" aria-label="Open menu">
    <i class="fa fa-bars"></i>
  </button>

  <!-- MAIN CONTENT -->
  <main class="main-content" id="mainContent" role="main">
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="footer mt-auto py-3 bg-light border-top text-center">
    <div class="container">
      <span class="text-muted">
        &copy; {{ date('Y') }} Sistem Monitoring & Pengingat Jadwal Perkuliahan — All Rights Reserved.
      </span>
    </div>
  </footer>

  <!-- SCRIPTS: jQuery, Popper, Bootstrap 4 -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <!-- Chart.js if used -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>


  <!-- Sidebar behavior + accessibility and responsive handling (from example) -->
  <script>
    (function($){
      var $sidebar = document.getElementById('sidebar');
      var $overlay = document.getElementById('sidebarOverlay');
      var $toggle = document.getElementById('btnSidebarToggle');
      var programmaticTabindexAdded = false;

      function setSidebarTransform(value) {
        if ($sidebar) $sidebar.style.transform = value;
      }

      function firstFocusableInSidebar(){
        var focusable = 'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])';
        var items = $sidebar.querySelectorAll(focusable);
        for (var i=0;i<items.length;i++){
          if (items[i].offsetParent !== null) return items[i];
        }
        return null;
      }

      function openSidebar(){
        setSidebarTransform('translateX(0)');
        $sidebar.setAttribute('aria-hidden','false');
        $overlay.classList.add('show');
        $overlay.setAttribute('aria-hidden','false');
        if ($toggle) $toggle.style.display = 'none';
        if ($toggle) $toggle.setAttribute('aria-expanded','true');
        var first = firstFocusableInSidebar();
        if (first) first.focus();
        else {
          $sidebar.setAttribute('tabindex','-1');
          programmaticTabindexAdded = true;
          $sidebar.focus();
        }
        document.body.style.overflow = 'hidden';
      }

      function closeSidebar(){
        if ($toggle) {
          $toggle.style.display = '';
          $toggle.focus();
          $toggle.setAttribute('aria-expanded','false');
        }
        setSidebarTransform('translateX(-110%)');
        $sidebar.setAttribute('aria-hidden','true');
        $overlay.classList.remove('show');
        $overlay.setAttribute('aria-hidden','true');
        if (programmaticTabindexAdded) {
          $sidebar.removeAttribute('tabindex');
          programmaticTabindexAdded = false;
        }
        document.body.style.overflow = '';
      }

      if ($toggle) {
        $toggle.addEventListener('click', function(e){
          e.preventDefault();
          var hidden = $sidebar.getAttribute('aria-hidden') === 'true';
          if (hidden) openSidebar(); else closeSidebar();
        });
      }

      if ($overlay) $overlay.addEventListener('click', function(){ closeSidebar(); });

      $sidebar.addEventListener('click', function(ev){
        var target = ev.target;
        while (target && target !== $sidebar){
          if (target.matches && target.matches('a.nav-link')) {
            if (window.innerWidth <= 991.98) closeSidebar();
            return;
          }
          target = target.parentNode;
        }
      });

      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && $sidebar.getAttribute('aria-hidden') === 'false') {
          closeSidebar();
        }
      });

      function handleResponsive() {
        var w = window.innerWidth;
        if (w >= 992) {
          setSidebarTransform('translateX(0)');
          $sidebar.setAttribute('aria-hidden','false');
          if ($toggle) $toggle.style.display = 'none';
          $overlay.classList.remove('show');
          $overlay.setAttribute('aria-hidden','true');
          document.body.style.overflow = '';
        } else {
          setSidebarTransform('translateX(-110%)');
          $sidebar.setAttribute('aria-hidden','true');
          if ($toggle) $toggle.style.display = '';
          $overlay.classList.remove('show');
          $overlay.setAttribute('aria-hidden','true');
          document.body.style.overflow = '';
        }
      }

      window.addEventListener('resize', handleResponsive);
      window.addEventListener('orientationchange', handleResponsive);
      document.addEventListener('DOMContentLoaded', function(){ handleResponsive(); });
      handleResponsive();
    })(window.jQuery || window);
  </script>

  <!-- Optional: clock script (kept from original but adapted minimal) -->
  <script>
    function updateClock() {
      const now = new Date();
      const options = { weekday: 'long', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
      const formatted = now.toLocaleTimeString('id-ID', options);
      // If page provides element with id="clockText", update it. Otherwise ignore.
      var el = document.getElementById('clockText');
      if (el) el.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

  @stack('scripts')
</body>
</html>
