<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ url('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>

        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('user') }}">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('pegawai') }}">Pegawai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('shift') }}">Shift</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('schedule') }}">Schedule</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#aktivitas" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-layout bi bi-person-workspace menu-icon"></i>
                <span class="menu-title">Aktivitas</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="aktivitas">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('absensi') }}">Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('perizinan') }}">Perizinan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('cuti') }}">Cuti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LKH</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false" aria-controls="ui-basic">
                <i class="icon-layout bi bi-file-earmark-text menu-icon"></i>
                <span class="menu-title">Laporan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="laporan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('laporan-shift') }}">Laporan Shift</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#">Laporan Rekap</a>
                    </li> --}}
                </ul>
            </div>
        </li>
    </ul>
</nav>
