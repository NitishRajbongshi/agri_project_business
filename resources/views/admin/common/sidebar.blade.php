<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style ="font-size: 17px;">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- Logo Here --}}
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Admin Control</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner">
        <!-- Dashboard -->
        <li class="menu-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Dashboard</div>
            </a>
        </li>

        <li
            class="menu-item {{ Request::routeIs('admin.office') ||
            Request::routeIs('admin.designation') ||
            Request::routeIs('admin.department') ||
            Request::routeIs('admin.role')
                ? 'open'
                : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Master" style ="font-size: 12px;">Admin</div>
            </a>
            
            <ul class="menu-sub">
                <a href="{{ route('admin.users') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">Users</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.usermanagement') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">User Role Management</div>
                </a>
            
            </ul>
            <ul class="menu-sub">
                <a href="{{ route('admin.rolemanager') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">User Role Manager</div>
                </a>
            </ul>
            <ul class="menu-sub">
                <a href="{{ route('admin.office') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Master" style ="font-size: 12px;">Office</div>
                </a>

                <a href="{{ route('admin.designation') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Master" style ="font-size: 12px;">Designation</div>
                </a>
            
                <a href="{{ route('admin.department') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Master" style ="font-size: 12px;">Department</div>
                </a>
            
                <a href="{{ route('admin.role') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Master" style ="font-size: 12px;">Role</div>
                </a>

                <a href="{{ route('agri-medicinal-products') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bx-terminal'></i>
                    <div data-i18n="Master" style ="font-size: 12px;">Medicinal Products</div>
                </a>
            </ul>
        </li>

        {{-- Application Master --}}
        <li class="menu-item {{ Request::routeIs('admin.appmaster.croptype') ||
            Request::routeIs('admin.appmaster.cropinformation') ||
            Request::routeIs('admin.appmaster.cropdisease') ||
            Request::routeIs('admin.appmaster.suitability') ||
            Request::routeIs('admin.appmaster.symptom') ||
            Request::routeIs('admin.appmaster.recommendation')
                ? 'open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layer"></i>
                <div data-i18n="Application-Master" style ="font-size: 12px;">Application Master</div>
            </a>

            {{-- Corp Type --}}
            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.croptype') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Crop Type</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.cropinformation') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Crop Name</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.cropdisease') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Crop Disease</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.suitability') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Suitability</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.symptom') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Symptom</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.appmaster.recommendation') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layer"></i>
                    <div data-i18n="Application-Master" style ="font-size: 12px;">Recommendation</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.cropsymptomdetails') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">Crop Symptom</div>
                </a>
            </ul>
            <ul class="menu-sub">
                <a href="{{ route('admin.allcropdetails') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">Crop Management</div>
                </a>
            </ul>
            <ul class="menu-sub">
                <a href="{{ route('admin.cropvarietydetails') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">Crop Variety</div>
                </a>
            </ul>

            <ul class="menu-sub">
                <a href="{{ route('admin.cropprotectiondetails') }}" class="menu-link">
                    <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                    <div data-i18n="Analytics" style ="font-size: 12px;">Crop Control Measure</div>
                </a>
            </ul>
        </li>

        <li class="menu-item {{ Request::routeIs('admin.reviewcropimage') ? 'active' : '' }}">
            <a href="{{ route('admin.reviewcropimage') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bxs-user-circle'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Verify Diagnosis</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('admin.filemanager') ? 'active' : '' }}">
            <a href="{{ route('admin.filemanager') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-folder-open'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">File Manager</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('moderator.queries') ? 'active' : '' }}">
            <a href="{{ route('moderator.queries') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bxl-quora'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Moderate Query</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('agriexpert.queriestoanswer') ? 'active' : '' }}">
            <a href="{{ route('agriexpert.queriestoanswer') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bxs-pen'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Answer Query</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('agrinews.newsmanager') || Request::routeIs('agrinews.categorymanager') ? 'active' : '' }}">
            <a href="{{ route('agrinews.newsmanager') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-news'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Agri News Manager</div>
            </a>
        </li>

        <li class="menu-item {{ Request::routeIs('misHomePage') ? 'active' : '' }}">
            <a href="{{ route('misHomePage') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bxs-file-export'></i>
                <div data-i18n="Analytics" style ="font-size: 12px;">Disease Report</div>
            </a>
        </li>
    </ul>
</aside>
