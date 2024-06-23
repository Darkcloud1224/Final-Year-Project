<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
    </a>
    <a href="{{ route('asset_recommendation') }}" class="nav-link {{ Request::is('asset_recommendation*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-lightbulb"></i>
        <p>Switchgear List</p>
    </a>
    <a href="{{ route('report_generation.index') }}" class="nav-link {{ Request::is('report_generation*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cloud-download-alt"></i>
        <p>Report Generation</p>
    </a>
    <a href="{{ route('report_log.index') }}" class="nav-link {{ Request::is('report_log*') ? 'active' : '' }}">
        <i class="nav-icon far fa-bookmark"></i>
        <p>Report Log</p>
    </a>   
    <a href="{{ route('approval_log.index') }}" class="nav-link {{ Request::is('approval_log*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-check-square"></i>
        <p>Approval Log</p>
    </a>
    <a href="{{ route('delete_request_logs.index') }}" class="nav-link {{ Request::is('delete_request_logs*') ? 'active' : '' }}">
        <i class="nav-icon far fa-address-card"></i>
        <p>Delete Request Log</p>
    </a>
    <a href="{{ route('switchgear_classification.index') }}" class="nav-link {{ Request::is('switchgear_classification*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-chart-bar"></i>
        <p>Switchgear Classification</p>
    </a>
    <a href="{{ route('switchgear_progress_monitoring.index') }}" class="nav-link {{ Request::is('switchgear_progress_monitoring*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Switchgear Progress Monitoring</p>
    </a>
</li>
