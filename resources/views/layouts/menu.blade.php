<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
    </a>
    <a href="{{ route('asset_recommendation') }}" class="nav-link {{ Request::is('asset_recommendation*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-lightbulb"></i>
        <p>Asset Maintenance Recommendation</p>
    </a>
    <a href="{{ route('report_generation.index') }}" class="nav-link {{ Request::is('report_generation*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cloud-download-alt"></i>
        <p>Report Generation</p>
    </a>
    <a href="{{ route('report_log.index') }}" class="nav-link {{ Request::is('report_log*') ? 'active' : '' }}">
        <i class="nav-icon far fa-bookmark"></i>
        <p>Report Log</p>
    </a>
    <a href="{{ route('approval.index') }}" class="nav-link {{ Request::is('approval*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-circle"></i>
        <p>Approval</p>
        @if($pendingApprovalsCount > 0)
            <span class="badge">({{ $pendingApprovalsCount }})</span>
        @endif
    </a>    
    <a href="{{ route('approval_log.index') }}" class="nav-link {{ Request::is('approval_log*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-check-square"></i>
        <p>Approval Log</p>
    </a>
</li>
