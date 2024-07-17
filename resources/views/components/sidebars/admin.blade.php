<li class="nav-item">
    <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <span class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" id="user">
                <path fill="#000" fill-rule="evenodd" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Zm3-12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 7a7.489 7.489 0 0 1 6-3 7.489 7.489 0 0 1 6 3 7.489 7.489 0 0 1-6 3 7.489 7.489 0 0 1-6-3Z" clip-rule="evenodd"></path>
            </svg>
        </span>
        <span class="nav-link-text">Usuarios</span> </a><!--//nav-link-->
</li>
<!--//nav-item-->
