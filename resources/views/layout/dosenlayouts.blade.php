<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>@yield('title','Dashboard') - {{ config('app.name','Laravel') }}</title>
  <link rel="icon" href="data:,">

  <!-- Bootstrap 4 & Font Awesome -->
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

    /* Sidebar base - CSS fallback only */
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
      /* DO NOT rely only on this - JS will set inline transform for reliability */
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

    /* Toggle (burger) */
    .btn-sidebar-toggle {
      position: fixed; top: 12px; left: 12px; z-index: 1060;
      display: none; border-radius:8px; padding:.5rem .6rem;
      background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.06);
    }
    .btn-sidebar-toggle:focus { outline: 3px solid rgba(13,110,253,0.12); }

    /* Layout main */
    .main-content { margin-left: var(--sidebar-width); padding: 28px; min-height: 100vh; background: #fff; transition: margin-left .18s ease; }

    /* Mobile/tablet: (CSS fallback) show toggle; JS will enforce hiding sidebar */
    @media (max-width: 991.98px) {
      .btn-sidebar-toggle { display:inline-block; padding:.6rem .7rem; border-radius:10px; }
      .main-content { margin-left: 0; padding-top: 20px; }
    }
    @media (max-width: 575.98px) {

  /* Global font scale */
  body {
    font-size: 13px;
  }

  /* Main content padding lebih kecil */
  .main-content {
    padding: 16px 14px;
  }

  /* Sidebar font & spacing */
  .sidebar {
    width: 220px;
  }

  .sidebar .app-title {
    font-size: 1.05rem;
    padding: 0 1rem;
  }

  .sidebar .nav-link {
    font-size: 0.9rem;
    padding: 0.65rem 1rem;
    gap: 10px;
  }

  .sidebar .nav-link i {
    font-size: 0.95rem;
  }

  /* Judul halaman (jika ada h1–h3 di konten) */
  h1 {
    font-size: 1.35rem;
  }

  h2 {
    font-size: 1.2rem;
  }

  h3 {
    font-size: 1.05rem;
  }

  /* Table & card lebih rapat */
  .table {
    font-size: 0.85rem;
  }

  .card-body {
    padding: 0.9rem;
  }

  /* Button lebih kecil */
  .btn {
    font-size: 0.85rem;
    padding: 0.35rem 0.6rem;
  }

  /* Form input */
  .form-control {
    font-size: 0.85rem;
    padding: 0.35rem 0.5rem;
  }
}

    /* Desktop: hide toggle */
    @media (min-width: 992px) {
      .btn-sidebar-toggle { display:none !important; }
      .main-content { margin-left: var(--sidebar-width); padding-top: 28px; }
    }

    /* Slightly larger tap targets */
    @media (max-width: 991.98px) {
      .sidebar .nav-link { padding: .9rem 1.2rem; font-size: 1rem; }
    }
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
        <a class="nav-link @if(request()->routeIs('dashboard*')) active @endif" href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('jadwal.*') || request()->routeIs('matkul.*')) active @endif" href="{{ route('jadwal.index') }}"><i class="fa fa-calendar"></i> Jadwal Saya</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('pengingat.*')) active @endif" href="{{ route('pengingat.index') }}"><i class="fa fa-bell"></i> Pengingat</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('profile.*')) active @endif" href="{{ route('profile.view') }}"><i class="fa fa-user"></i> Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a>
      </li>
    </ul>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    <div class="sidebar-footer text-center">
      <small>© {{ date('Y') }} {{ config('app.name') }}</small>
    </div>
  </aside>

  <!-- Overlay (no tabindex) -->
  <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

  <!-- Toggle button (placed AFTER sidebar so we can hide it if sidebar visible) -->
  <button class="btn btn-light btn-sidebar-toggle" id="btnSidebarToggle" aria-controls="sidebar" aria-expanded="false" aria-label="Open menu">
    <i class="fa fa-bars"></i>
  </button>

  <!-- MAIN CONTENT -->
  <main class="main-content" id="mainContent" role="main">
    @yield('content')
  </main>

  <!-- SCRIPTS -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <script>
    (function($){
      var $sidebar = document.getElementById('sidebar');
      var $overlay = document.getElementById('sidebarOverlay');
      var $toggle = document.getElementById('btnSidebarToggle');
      var programmaticTabindexAdded = false;

      // helper to set inline transform reliably
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
        // show overlay and sidebar using inline style (forced)
        setSidebarTransform('translateX(0)');
        $sidebar.setAttribute('aria-hidden','false');
        $overlay.classList.add('show');
        $overlay.setAttribute('aria-hidden','false');

        // hide toggle button visually
        if ($toggle) $toggle.style.display = 'none';
        if ($toggle) $toggle.setAttribute('aria-expanded','true');

        // focus first element or sidebar
        var first = firstFocusableInSidebar();
        if (first) first.focus();
        else {
          $sidebar.setAttribute('tabindex','-1');
          programmaticTabindexAdded = true;
          $sidebar.focus();
        }

        // prevent body scroll
        document.body.style.overflow = 'hidden';
      }

      function closeSidebar(){
        // move focus back to toggle BEFORE hiding the sidebar for accessibility
        if ($toggle) {
          $toggle.style.display = ''; // restore to CSS-controlled display (JS will adjust later)
          $toggle.focus();
          $toggle.setAttribute('aria-expanded','false');
        }

        // hide overlay and move sidebar out (force)
        setSidebarTransform('translateX(-110%)');
        $sidebar.setAttribute('aria-hidden','true');
        $overlay.classList.remove('show');
        $overlay.setAttribute('aria-hidden','true');

        // remove programmatic tabindex
        if (programmaticTabindexAdded) {
          $sidebar.removeAttribute('tabindex');
          programmaticTabindexAdded = false;
        }

        // restore body scroll
        document.body.style.overflow = '';
      }

      // Toggle click handler
      if ($toggle) {
        $toggle.addEventListener('click', function(e){
          e.preventDefault();
          // check current transform to detect open state
          var current = window.getComputedStyle($sidebar).transform;
          // If transform corresponds to identity or translateX(0) consider open
          if (current !== 'none') {
            // computed transform string might be matrix(...) when open/closed; safer to check inline style
          }
          // We'll use aria-hidden attribute to decide
          var hidden = $sidebar.getAttribute('aria-hidden') === 'true';
          if (hidden) openSidebar(); else closeSidebar();
        });
      }

      // overlay click closes
      if ($overlay) $overlay.addEventListener('click', function(){ closeSidebar(); });

      // close when clicking nav link on small screens
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

      // ESC key
      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && $sidebar.getAttribute('aria-hidden') === 'false') {
          closeSidebar();
        }
      });

      // Responsive handler: enforce inline transform based on breakpoint
      function handleResponsive() {
        var w = window.innerWidth;
        if (w >= 992) {
          // Desktop: sidebar visible; ensure inline transform shows it and hide toggle
          setSidebarTransform('translateX(0)');
          $sidebar.setAttribute('aria-hidden','false');
          if ($toggle) $toggle.style.display = 'none';
          $overlay.classList.remove('show');
          $overlay.setAttribute('aria-hidden','true');
          document.body.style.overflow = '';
        } else {
          // Tablet/phone: sidebar hidden by default; show toggle
          setSidebarTransform('translateX(-110%)');
          $sidebar.setAttribute('aria-hidden','true');
          if ($toggle) $toggle.style.display = ''; // show according CSS
          $overlay.classList.remove('show');
          $overlay.setAttribute('aria-hidden','true');
          document.body.style.overflow = '';
        }
      }

      window.addEventListener('resize', handleResponsive);
      window.addEventListener('orientationchange', handleResponsive);
      document.addEventListener('DOMContentLoaded', function(){ handleResponsive(); });
      // call on load as well
      handleResponsive();
    })(window.jQuery || window);
    
  </script>

  @stack('scripts')
</body>
</html>
