<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <!-- Dashboards -->

            <li class="menu-item {{ Request::is('dashboard*') ? 'active open' : '' }}">
                <a href="/dashboard" class="menu-link">
                    <i class="menu-icon icon-base ri ri-home-smile-line"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('driver*') ? 'active open' : '' }}">
                <a href="{{ route('driver.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-user-line"></i>
                    <div data-i18n="Driver">Driver</div>
                </a>
            </li>

            <li class="menu-item {{ Request::is('mobil*') ? 'active open' : '' }}">
                <a href="{{ route('mobil.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-car-line"></i>
                    <div data-i18n="Mobil">Mobil</div>
                </a>
            </li>

            <li class="menu-item {{ Request::is('surat-jalan*') ? 'active open' : '' }}">
                <a href="{{ route('surat-jalan.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                    <div data-i18n="Pesan Mobil">Pesan Mobil</div>
                </a>
            </li>

            <li class="menu-item {{ Request::is('dokumen*') ? 'active open' : '' }}">
                <a href="{{ route('dokumen.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                    <div data-i18n="Dokumen">Dokumen</div>
                </a>
            </li>
        </ul>
    </div>
</aside>