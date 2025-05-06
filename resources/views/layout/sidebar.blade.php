<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <!-- Dashboards -->

            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link">
                    <i class="menu-icon icon-base ri ri-home-smile-line"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item active">
                        <a href="dashboards-crm.html" class="menu-link">
                            <i class="menu-icon icon-base ri ri-computer-line"></i>
                            <div data-i18n="CRM">CRM</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="index.html" class="menu-link">
                            <i class="menu-icon icon-base ri ri-bar-chart-line"></i>
                            <div data-i18n="Analytics">Analytics</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-ecommerce-dashboard.html" class="menu-link">
                            <i class="menu-icon icon-base ri ri-shopping-cart-2-line"></i>
                            <div data-i18n="eCommerce">eCommerce</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-logistics-dashboard.html" class="menu-link">
                            <i class="menu-icon icon-base ri ri-truck-line"></i>
                            <div data-i18n="Logistics">Logistics</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-academy-dashboard.html" class="menu-link">
                            <i class="menu-icon icon-base ri ri-book-open-line"></i>
                            <div data-i18n="Academy">Academy</div>
                        </a>
                    </li>
                </ul>
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

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon icon-base ri ri-car-line"></i>
                    <div data-i18n="Mobil">Pemesanan Mobil</div>
                </a>
            </li>
        </ul>
    </div>
</aside>