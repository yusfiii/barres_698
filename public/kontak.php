<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - BARRES 698</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --jet-black: #0D0D0D;
            --dark-grey: #2A2A2A;
            --gold: #F7B801;
            --gold-dark: #E0A600;
            --off-white: #F5F5F5;
            --off-white-dim: #E8E5DF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--off-white);
            color: var(--jet-black);
            overflow-x: hidden;
        }

        /* Navbar */
        .site-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 40px;
            background: rgba(13, 13, 13, 0.96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(247, 184, 1, 0.25);
            transition: padding .3s;
        }

        .site-nav.compact {
            padding: 12px 40px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--jet-black);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .nav-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-logo-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 1px;
            color: #fff;
            line-height: 1;
        }

        .nav-logo-sub {
            font-size: .6rem;
            color: rgba(255, 255, 255, .45);
            letter-spacing: 3px;
            text-transform: uppercase;
            display: block;
            font-weight: 400;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a {
            font-size: .82rem;
            font-weight: 500;
            letter-spacing: .5px;
            color: rgba(255, 255, 255, .65);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 8px;
            transition: color .2s, background .2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #fff;
            background: rgba(255, 255, 255, .07);
        }

        .nav-cta {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
            color: var(--jet-black) !important;
            padding: 7px 18px !important;
            font-weight: 600 !important;
        }

        .nav-cta:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .nav-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            margin: 5px 0;
            transition: all .3s;
        }

        /* Hero Section Kontak */
        .page-hero {
            background: var(--jet-black);
            padding: 140px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 55%;
            height: 140%;
            background: linear-gradient(160deg, rgba(247, 184, 1, 0.08) 0%, rgba(247, 184, 1, 0.02) 60%, transparent 100%);
            transform: skewX(-8deg);
            z-index: 0;
        }

        .page-hero .container {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 24px;
        }

        .hero-badge::before {
            content: '';
            display: block;
            width: 28px;
            height: 2px;
            background: var(--gold);
        }

        .page-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(2.5rem, 5vw, 4rem);
            color: #fff;
            margin-bottom: 20px;
        }

        .page-hero .lead {
            color: rgba(255, 255, 255, .5);
            font-size: 1.1rem;
            max-width: 600px;
        }

        /* Section Umum */
        .section {
            padding: 80px 0;
        }

        .section-dark {
            background: var(--jet-black);
            color: var(--off-white);
        }

        .section-light {
            background: var(--off-white);
        }

        .section-label {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(247, 184, 1, 0.3);
            max-width: 60px;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .section-light .section-title {
            color: var(--jet-black);
        }

        .section-text {
            font-size: 1rem;
            line-height: 1.8;
            color: #4a4540;
        }

        /* Contact Cards */
        .contact-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            border: 1px solid rgba(247, 184, 1, 0.15);
            transition: all .3s;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            cursor: pointer;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 12px 30px rgba(247, 184, 1, 0.1);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: rgba(247, 184, 1, 0.12);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .contact-icon i {
            font-size: 28px;
            color: var(--gold);
        }

        .contact-card h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--jet-black);
            margin-bottom: 16px;
        }

        .contact-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .contact-card .contact-value {
            font-weight: 500;
            color: var(--jet-black);
        }

        .emergency-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 12px;
            padding: 4px 12px;
            font-size: .75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 12px;
        }

        /* Card Link Wrapper */
        .contact-card-link {
            text-decoration: none !important;
            display: block;
            height: 100%;
        }

        /* Form Card */
        .form-card {
            background: var(--dark-grey);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(247, 184, 1, 0.12);
        }

        .form-card h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 8px;
        }

        .form-card .subtitle {
            color: rgba(255, 255, 255, .5);
            margin-bottom: 32px;
        }

        .form-control-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(247, 184, 1, 0.2);
            border-radius: 12px;
            padding: 14px 18px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            transition: all .3s;
        }

        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--gold);
            outline: none;
            box-shadow: 0 0 0 3px rgba(247, 184, 1, 0.2);
            color: #fff;
        }

        .form-control-custom::placeholder {
            color: rgba(255, 255, 255, .3);
        }

        textarea.form-control-custom {
            resize: vertical;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--jet-black);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            border: none;
            transition: all .3s;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(247, 184, 1, 0.35);
        }

        /* Map Container */
        .map-container {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(247, 184, 1, 0.2);
            margin-top: 60px;
        }

        .map-container iframe {
            width: 100%;
            height: 450px;
            display: block;
        }

        /* Footer */
        .site-footer {
            background: var(--jet-black);
            padding: 60px 0 32px;
            border-top: 1px solid rgba(247, 184, 1, 0.1);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .footer-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-brand-icon i {
            color: var(--jet-black);
            font-size: 1.1rem;
        }

        .footer-brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            color: #fff;
            line-height: 1;
        }

        .footer-brand-tagline {
            font-size: .65rem;
            color: rgba(255, 255, 255, .35);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .footer-desc {
            font-size: .87rem;
            line-height: 1.75;
            max-width: 300px;
            color: rgba(255, 255, 255, .3);
        }

        .footer-heading {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            font-size: .88rem;
            color: rgba(255, 255, 255, .4);
            text-decoration: none;
            transition: color .2s;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .85rem;
            color: rgba(255, 255, 255, .4);
            margin-bottom: 12px;
        }

        .footer-contact-item i {
            color: var(--gold);
            width: 16px;
        }

        .emergency-box {
            background: rgba(247, 184, 1, 0.08);
            border: 1px solid rgba(247, 184, 1, 0.2);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }

        .emergency-box .label {
            font-size: .65rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
            font-family: 'DM Mono', monospace;
            margin-bottom: 8px;
        }

        .emergency-box .number {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 3rem;
            color: var(--gold);
            line-height: 1;
        }

        .footer-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .07);
            margin: 40px 0 24px;
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .78rem;
            color: rgba(255, 255, 255, .3);
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .4);
            text-decoration: none;
            transition: all .2s;
        }

        .footer-socials a:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--jet-black);
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }

        @media (max-width: 992px) {
            .site-nav {
                padding: 16px 24px;
            }

            .nav-links {
                display: none;
            }

            .nav-toggle {
                display: block;
            }

            .nav-links.open {
                display: flex;
                flex-direction: column;
                gap: 4px;
                position: fixed;
                top: 72px;
                left: 0;
                right: 0;
                background: rgba(13, 13, 13, 0.98);
                padding: 20px 24px;
                border-bottom: 1px solid rgba(247, 184, 1, 0.15);
            }

            .footer-bottom {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .form-card {
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="site-nav" id="siteNav">
        <a class="nav-logo" href="index.php">
            <div class="nav-logo-icon">
                <img src="../assets/barres2.png" alt="BARRES Logo">
            </div>
            <div>
                <span class="nav-logo-text">BARRES 698</span>
                <span class="nav-logo-sub">Banjarbaru Rescue</span>
            </div>
        </a>

        <div class="nav-links" id="navLinks">
            <a href="index.php">Beranda</a>
            <a href="profil.php">Profil</a>
            <a href="peta-statistik.php">Peta & Statistik</a>
            <a href="kontak.php" class="active">Kontak</a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                <?php if ($_SESSION['role'] == 'super_admin'): ?>
                    <a href="../admin/dashboard.php" class="nav-cta">Dashboard</a>
                <?php else: ?>
                    <a href="../bpk/dashboard.php" class="nav-cta">Dashboard</a>
                <?php endif; ?>
                <a href="../logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-cta">Login Admin</a>
            <?php endif; ?>
        </div>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-badge reveal">Hubungi Kami</div>
            <h1 class="reveal">Kontak <span style="color: var(--gold);">BARRES 698</span></h1>
            <p class="lead reveal">Siap sedia 24 jam untuk melayani dan membantu masyarakat Kota Banjarbaru</p>
        </div>
    </section>

    <!-- CONTACT INFO SECTION -->
    <section class="section section-light">
        <div class="container">
            <div class="row g-4">
                
                <!-- Card 1: Alamat -->
                <div class="col-md-6 col-lg-3 reveal">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Alamat</h3>
                        <p>Jl. Zafri Zam-Zam II, Guntungmanggis</p>
                        <p class="contact-value">Kec. Landasan Ulin, Kota Banjar Baru, Kalsel 70714</p>
                    </div>
                </div>

                <!-- Card 2: WhatsApp (Direct Link) -->
                <div class="col-md-6 col-lg-3 reveal">
                    <a href="https://wa.me/6285111315698" target="_blank" class="contact-card-link">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h3>WhatsApp</h3>
                            <p>Layanan Cepat:</p>
                            <p class="contact-value"><strong>0851 1131 5698</strong> <span class="emergency-badge">24 Jam</span></p>
                        </div>
                    </a>
                </div>

                <!-- Card 3: Email (Direct Link) -->
                <div class="col-md-6 col-lg-3 reveal">
                    <a href="mailto:barres698.banjarbaru@gmail.com" class="contact-card-link">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3>Email</h3>
                            <p>Kirimkan email ke:</p>
                            <p class="contact-value" style="word-break: break-all;">barres698.banjarbaru@gmail.com</p>
                        </div>
                    </a>
                </div>

                <!-- Card 4: Instagram (Direct Link) -->
                <div class="col-md-6 col-lg-3 reveal">
                    <a href="https://instagram.com/barres698_bjb" target="_blank" class="contact-card-link">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <h3>Instagram</h3>
                            <p>Follow update kami:</p>
                            <p class="contact-value">@barres698_bjb</p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- MAP SECTION FULL WIDTH -->
    <section class="section section-dark">
        <div class="container">
            <div class="row justify-content-center reveal">
                <div class="col-lg-10">
                    <div class="form-card" style="padding: 0; overflow: hidden;">
                        <div style="padding: 32px 24px 0 24px; text-align: center;">
                            <h3>Lokasi Kami</h3>
                            <p class="subtitle mb-4">Sekretariat BARRES 698 Banjarbaru</p>
                        </div>
                        <div class="map-container" style="margin-top: 0; border-radius: 0;">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3604.014334922858!2d114.82541947449964!3d-3.4552614418426977!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de681001af803a3%3A0x23bebf872b30d0a1!2sSekretariat%20Banjarbaru%20Rescue%20698%20(BARRES%20698)!5e1!3m2!1sid!2sid!4v1784186964959!5m2!1sid!2sid" 
                                width="100%" 
                                height="450" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="strict-origin-when-cross-origin">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="footer-brand-icon"><i class="fas fa-fire"></i></div>
                        <div>
                            <div class="footer-brand-name">BARRES 698</div>
                            <div class="footer-brand-tagline">Banjarbaru Rescue</div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Sistem Informasi Geografis pemetaan lokasi kebakaran berbasis web dengan metode Kernel Density Estimation (KDE).
                    </p>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="footer-heading">Menu</div>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="peta-statistik.php">Peta & Statistik</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="footer-heading">Kontak</div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Kota Banjarbaru, Kalimantan Selatan</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(0851) 11315698</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>barres698.banjarbaru@gmail.com</span>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="footer-heading">Darurat</div>
                    <div class="emergency-box">
                        <div class="label">Pemadam Kebakaran</div>
                        <div class="number">112</div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> BARRES 698 — SIG Pemetaan Kebakaran Banjarbaru</span>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll compact
        const nav = document.getElementById('siteNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('compact', window.scrollY > 60);
        });

        // Hamburger toggle
        const toggle = document.getElementById('navToggle');
        const links = document.getElementById('navLinks');
        toggle.addEventListener('click', () => links.classList.toggle('open'));

        // Reveal on scroll
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    setTimeout(() => e.target.classList.add('visible'), i * 60);
                    observer.unobserve(e.target);
                }
            });
        }, {
            threshold: 0.12
        });

        reveals.forEach(el => observer.observe(el));

        // Hero reveals on load
        document.querySelectorAll('.page-hero .reveal').forEach((el, i) => {
            setTimeout(() => el.classList.add('visible'), 200 + i * 100);
        });
    </script>
</body>

</html>