<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <a href="{{ setRoute('admin.dashboard') }}" class="sidebar-main-logo">
                <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings, 'white') }}"
                    data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
            </a>
            <button class="sidebar-menu-bar">
                <i class="fas fa-exchange-alt"></i>
            </button>
        </div>
        <div class="sidebar-user-area">
            <div class="sidebar-user-thumb">
                <a href="{{ setRoute('admin.profile.index') }}"><img
                        src="{{ get_image(Auth::user()->image, 'admin-profile', 'profile') }}" alt="user"></a>
            </div>
            <div class="sidebar-user-content">
                <h6 class="title">{{ Auth::user()->fullname }}</h6>
                <span class="sub-title">{{ Auth::user()->getRolesString() }}</span>
            </div>
        </div>
        @php
            $current_route = Route::currentRouteName();
        @endphp
        <div class="sidebar-menu-wrapper">
            <ul class="sidebar-menu">

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.dashboard',
                    'title' => __('Dashboard'),
                    'icon' => 'menu-icon las la-rocket',
                ])

                {{-- Section Default --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Default'),
                    'group_links' => [
                        [
                            'title' => __('Setup Currency'),
                            'route' => 'admin.currency.index',
                            'icon' => 'menu-icon las la-coins',
                        ],
                        [
                            'title' => __('Exchange Rate'),
                            'route' => 'admin.exchange.rate.index',
                            'icon' => 'menu-icon las la-exchange-alt',
                        ],
                        [
                            'title' => __('Fees & Charges'),
                            'route' => 'admin.trx.settings.index',
                            'icon' => 'menu-icon las la-wallet',
                        ],
                        [
                            'title' => __('Gift Card API'),
                            'route' => 'admin.gift.card.index',
                            'icon' => 'menu-icon las la-gift',
                        ],
                        [
                            'title' => __('Mobile TopUp & Bundle Method'),
                            'icon' => 'menu-icon las la-mobile',
                            'route' => 'admin.mobile.topup.method.automatic.index',
                        ],
                        [
                            'title' => __('Quick Add Money'),
                            'route' => 'admin.quick.recharge.index',
                            'icon' => 'menu-icon las la-bolt',
                        ],
                        [
                            'title' => __('Quick Topup'),
                            'route' => 'admin.short.topup.index',
                            'icon' => 'menu-icon las la-bolt',
                        ],
                    ],
                ])

                {{-- Section Transaction & Logs --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Transactions & Logs'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Add Money Logs'),
                                'icon' => 'menu-icon las la-calculator',
                                'links' => [
                                    [
                                        'title' => __('Pending Logs'),
                                        'route' => 'admin.add.money.pending',
                                    ],
                                    [
                                        'title' => __('Completed Logs'),
                                        'route' => 'admin.add.money.complete',
                                    ],
                                    [
                                        'title' => __('Canceled Logs'),
                                        'route' => 'admin.add.money.canceled',
                                    ],
                                    [
                                        'title' => __('All Logs'),
                                        'route' => 'admin.add.money.index',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => __('Gift Card Logs'),
                            'icon' => 'menu-icon las la-gift',
                            'route' => 'admin.gift.card.logs',
                        ],
                        [
                            'title' => __('Mobile Topup Logs'),
                            'icon' => 'menu-icon las la-mobile',
                            'route' => 'admin.mobile.topup.index',
                        ],
                        [
                            'title' => __('Data Bundle Logs'),
                            'icon' => 'menu-icon las la-mobile',
                            'route' => 'admin.data.bundle.index',
                        ],
                        [
                            'title' => __('Admin Profit Logs'),
                            'icon' => 'menu-icon las la-coins',
                            'route' => 'admin.profit.logs.index',
                        ],
                    ],
                ])
                {{-- Interface Panel --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Interface Panel'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('User Care'),
                                'icon' => 'menu-icon las la-user-edit',
                                'links' => [
                                    [
                                        'title' => __('Active Users'),
                                        'route' => 'admin.users.active',
                                    ],
                                    [
                                        'title' => __('Phone Unverified'),
                                        'route' => 'admin.users.phone.unverified',
                                    ],
                                    [
                                        'title' => __('KYC Unverified'),
                                        'route' => 'admin.users.kyc.unverified',
                                    ],
                                    [
                                        'title' => __('All Users'),
                                        'route' => 'admin.users.index',
                                    ],
                                    [
                                        'title' => __('Email To Users'),
                                        'route' => 'admin.users.email.users',
                                    ],
                                    [
                                        'title' => __('Banned Users'),
                                        'route' => 'admin.users.banned',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Admin Care'),
                                'icon' => 'menu-icon las la-user-shield',
                                'links' => [
                                    [
                                        'title' => __('All Admin'),
                                        'route' => 'admin.admins.index',
                                    ],
                                    [
                                        'title' => __('Admin Role'),
                                        'route' => 'admin.admins.role.index',
                                    ],
                                    [
                                        'title' => __('Role Permission'),
                                        'route' => 'admin.admins.role.permission.index',
                                    ],
                                    [
                                        'title' => __('Email To Admin'),
                                        'route' => 'admin.admins.email.admins',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                {{-- Section Settings --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Settings'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Web Settings'),
                                'icon' => 'menu-icon lab la-safari',
                                'links' => [
                                    [
                                        'title' => __('Basic Settings'),
                                        'route' => 'admin.web.settings.basic.settings',
                                    ],
                                    [
                                        'title' => __('Image Assets'),
                                        'route' => 'admin.web.settings.image.assets',
                                    ],
                                    [
                                        'title' => __('Setup SEO'),
                                        'route' => 'admin.web.settings.setup.seo',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('App Settings'),
                                'icon' => 'menu-icon las la-mobile',
                                'links' => [
                                    [
                                        'title' => __('Splash Screen'),
                                        'route' => 'admin.app.settings.splash.screen',
                                    ],
                                    [
                                        'title' => __('Onboard Screen'),
                                        'route' => 'admin.app.settings.onboard.screens',
                                    ],
                                    [
                                        'title' => __('App URLs'),
                                        'route' => 'admin.app.settings.urls',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.languages.index',
                    'title' => __('Languages'),
                    'icon' => 'menu-icon las la-language',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.system.maintenance.index',
                    'title' => 'System Maintenance',
                    'icon' => 'menu-icon las la-tools',
                ])
                {{-- Verification Center --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Verification Center'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Setup Email'),
                                'icon' => 'menu-icon las la-envelope-open-text',
                                'links' => [
                                    [
                                        'title' => __('Email Method'),
                                        'route' => 'admin.setup.email.config',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Setup SMS'),
                                'icon' => 'menu-icon las la-sms',
                                'links' => [
                                    [
                                        'title' => __('SMS Method'),
                                        'route' => 'admin.setup.sms.config',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.setup.kyc.index',
                    'title' => __('Setup KYC'),
                    'icon' => 'menu-icon las la-clipboard-list',
                ])

                @if (admin_permission_by_name('admin.setup.sections.section'))
                    <li class="sidebar-menu-header">{{ __('Setup Web Content') }}</li>
                    @php
                        $current_url = URL::current();

                        $setup_section_childs = [
                            setRoute('admin.setup.sections.section', 'auth-section'),
                            setRoute('admin.setup.sections.section', 'banner'),
                            setRoute('admin.setup.sections.section', 'services'),
                            setRoute('admin.setup.sections.section', 'how-it-work'),
                            setRoute('admin.setup.sections.section', 'security'),
                            setRoute('admin.setup.sections.section', 'download-app'),
                            setRoute('admin.setup.sections.section', 'statistic'),
                            setRoute('admin.setup.sections.section', 'about-us'),
                            setRoute('admin.setup.sections.section', 'faq'),
                            setRoute('admin.setup.sections.section', 'service-page'),
                            setRoute('admin.setup.sections.section', 'gift-card'),
                            setRoute('admin.setup.sections.section', 'air-time'),
                            setRoute('admin.setup.sections.section', 'blog'),
                            setRoute('admin.setup.sections.section', 'brand'),
                            setRoute('admin.setup.sections.section', 'feature'),
                            setRoute('admin.setup.sections.section', 'clients-feedback'),
                            setRoute('admin.setup.sections.section', 'announcement'),
                            setRoute('admin.setup.sections.section', 'contact-us'),
                            setRoute('admin.setup.sections.section', 'footer'),
                            setRoute('admin.setup.sections.section', 'about-page'),
                        ];
                    @endphp

                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url, $setup_section_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-terminal"></i>
                            <span class="menu-title">{{ __('Setup Section') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.setup.sections.section', 'auth-section') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'auth-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Auth Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'banner') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'banner')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Banner Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'how-it-work') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'how-it-work')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('How It Work Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'security') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'security')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Security Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'download-app') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'download-app')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Download App Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'statistic') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'statistic')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Statistic Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'about-us') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'about-us')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('About Us Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'faq') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'faq')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('FAQ Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'service-page') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'service-page')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Services Page Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'gift-card') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'gift-card')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Gift Card Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'air-time') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'air-time')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Mobile Topup Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'blog') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'blog')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Blog Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'contact-us') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'contact-us')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Contact US Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'footer') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'footer')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Footer Section') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.setup.pages.index',
                    'title' => __('Setup Pages'),
                    'icon' => 'menu-icon las la-file-alt',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.extensions.index',
                    'title' => __('Extensions'),
                    'icon' => 'menu-icon las la-puzzle-piece',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.useful.links.index',
                    'title' => __('Useful Links'),
                    'icon' => 'menu-icon las la-link',
                ])

                @if (admin_permission_by_name('admin.payment.gateway.view'))
                    <li class="sidebar-menu-header">{{ __('Payment Methods') }}</li>
                    @php
                        $payment_add_money_childs = [
                            setRoute('admin.payment.gateway.view', ['add-money', 'automatic']),
                            setRoute('admin.payment.gateway.view', ['add-money', 'manual']),
                        ];
                    @endphp
                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url, $payment_add_money_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-funnel-dollar"></i>
                            <span class="menu-title">{{ __('Add Money') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.payment.gateway.view', ['add-money', 'automatic']) }}"
                                    class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view', ['add-money', 'automatic'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Automatic') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.payment.gateway.view', ['add-money', 'manual']) }}"
                                    class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view', ['add-money', 'manual'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Manual') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Notifications --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Notification'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Push Notification'),
                                'icon' => 'menu-icon las la-bell',
                                'links' => [
                                    [
                                        'title' => __('Setup Notification'),
                                        'route' => 'admin.push.notification.config',
                                    ],
                                    [
                                        'title' => __('Send Notification'),
                                        'route' => 'admin.push.notification.index',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => __('Contact Messages'),
                            'icon' => 'menu-icon las la-sms',
                            'route' => 'admin.contact.messages.index',
                        ],
                    ],
                ])

                @php
                    $bonus_routes = ['admin.cookie.index', 'admin.server.info.index', 'admin.cache.clear'];
                @endphp

                @if (admin_permission_by_name_array($bonus_routes))
                    <li class="sidebar-menu-header">{{ __('Bonus') }}</li>
                @endif

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.cookie.index',
                    'title' => __('GDPR Cookie'),
                    'icon' => 'menu-icon las la-cookie-bite',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.server.info.index',
                    'title' => __('Server Info'),
                    'icon' => 'menu-icon las la-sitemap',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.cache.clear',
                    'title' => __('Clear Cache'),
                    'icon' => 'menu-icon las la-broom',
                ])
            </ul>
        </div>
    </div>
</div>
