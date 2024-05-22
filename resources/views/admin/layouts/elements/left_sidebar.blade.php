<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="app-brand-logo demo">
                <img src="{{asset('assets/admin/img/favicon/logo.png')}}" alt="" class="dashboard-logo">
			</span>
			<span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('admin.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/users') ? 'active' : ''}}">
			<a href="{{route('admin.users.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-group"></i>
				<div data-i18n="User">Users</div>
			</a>
		</li>
        <li class="menu-item {{ request()->is('admin/transactions') ? 'active' : ''}}">
			<a href="{{route('admin.transaction.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-list-ul"></i>
				<div data-i18n="transactions">All Transactions</div>
			</a>
		</li>

        <li class="menu-item {{ request()->is('admin/questions*') ? 'active' : ''}}">
			<a href="{{route('admin.questions.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-list-ul"></i>
				<div data-i18n="questions">Questions</div>
			</a>
		</li>

        <li class="menu-item {{ request()->is('admin/competition*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-book-content"></i>
				<div data-i18n="Pages">Competitions</div>
			</a>
			<ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/competition/live') ? 'active' : ''}}">
                    <a href="{{route('admin.competition.live')}}" class="menu-link">
                        <div data-i18n="Live">Live</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('admin/competition/completed') ? 'active' : ''}}">
                    <a href="{{route('admin.competition.completed')}}" class="menu-link">
                        <div data-i18n="Completed">Completed</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('admin/competition/upcoming') ? 'active' : ''}}">
                    <a href="{{route('admin.competition.upcoming')}}" class="menu-link">
                        <div data-i18n="Upcoming">Upcoming</div>
                    </a>
                </li>
			</ul>
		</li>

		{{-- <li class="menu-item {{ request()->is('admin/notifications/index') ? 'active' : ''}}">
			<a href="{{route('admin.notifications.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-bell"></i>
				<div data-i18n="Notifications">Notifications</div>
			</a>
		</li> --}}

		{{-- <li class="menu-item {{ request()->is('admin/contacts') ? 'active' : ''}}">
			<a href="{{route('admin.contacts.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-envelope"></i>
				<div data-i18n="Contacts">Contacts</div>
			</a>
		</li> --}}


        {{-- <li class="menu-item {{ request()->is('admin/how-to-play*') ? 'active' : ''}}">
			<a href="{{route('admin.how-to-play.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-list-ul"></i>
				<div data-i18n="HowToPLay">How to Play</div>
			</a>
		</li> --}}

		 @php
            $pages = Helper::pages();
        @endphp

		<li class="menu-item {{ request()->is('admin/page*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-book-content"></i>
				<div data-i18n="Pages">CMS</div>
				{{-- <div class="badge bg-danger rounded-pill ms-auto">{{count($pages)}}</div> --}}
			</a>
			<ul class="menu-sub">
				@foreach($pages as $page)
					<li class="menu-item {{ request()->is('admin/page/create/'.$page->key) ? 'active' : ''}}">
						<a href="{{route('admin.page.create',$page->key)}}" class="menu-link">
							<div data-i18n="{{$page->name}}">{{$page->name}}</div>
						</a>
					</li>
                @endforeach
			</ul>
		</li>

	</ul>
</aside>
