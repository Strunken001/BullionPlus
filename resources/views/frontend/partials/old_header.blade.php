<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container custom-container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title" href="index.html"><img src="{{ asset('public/frontend/images/logo/logo.png') }}" alt="site-logo"></a>
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto">
                                <li><a href="index.html" class="active">Home</a></li>
                                <li><a href="nfc.html">NFC Payment</a></li>
                                <li><a href="about.html">About</a></li>
                                <li><a href="#0">Announcement <i class="las la-angle-down ms-1"></i></a>
                                    <ul class="sub-menu">
                                        <li><a href="blog.html">Announcement</a></li>
                                        <li><a href="blog-details.html">Single Announcement</a></li>
                                    </ul>
                                </li>
                                <li><a href="developers.html">Developers</a></li>
                                <li><a href="contact.html">Contact</a></li>
                            </ul>
                            <div class="header-action">
                                <a href="{{ setRoute('user.login') }}" class="btn--base">{{ __("Login Now") }}</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->