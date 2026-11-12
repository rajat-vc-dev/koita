<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('assets/admin/images/kcadt-ai-awards-logo.webp') }}" alt="logo">
            </a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="ti-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.applicants.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.applicants.index') }}">
                            <i class="ti-user"></i>
                            <span>Registrations</span>
                        </a>
                    </li>
                    @if (config('koita.shortlist_enabled'))
                        <li class="{{ request()->routeIs('admin.shortlist.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.shortlist.index') }}">
                                <i class="ti-list"></i>
                                <span>Shortlist</span>
                            </a>
                        </li>
                    @endif
                    <li class="{{ request()->routeIs('admin.scoreboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.scoreboard') }}">
                            <i class="ti-bar-chart"></i>
                            <span>Scoreboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.email-templates.index') }}">
                            <i class="ti-email"></i>
                            <span>Email Templates</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.page-templates.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.page-templates.index') }}">
                            <i class="ti-file"></i>
                            <span>Thank You Pages</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
