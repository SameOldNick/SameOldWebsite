<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <img class="img-profile rounded-circle" src="https://via.placeholder.com/150x150">
    <span class="ms-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->email }} <i class="fa-solid fa-caret-down"></i></span>
</a>
<!-- Dropdown - User Information -->
<div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
    aria-labelledby="userDropdown">
    <a class="dropdown-item" href="{{ route('user.profile') }}">
        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
        {{ __('Profile') }}
    </a>
    <a class="dropdown-item" href="#">
        <i class="fas fa-key fa-sm fa-fw me-2 text-gray-400"></i>
        {{ __('Change Password') }}
    </a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
        {{ __('Logout') }}
    </a>
</div>
