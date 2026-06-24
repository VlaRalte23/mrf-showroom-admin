<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'MRF Showroom Admin') }}</title>

        <style>
            :root {
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: #18212f;
                background: #f5f7f8;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .header {
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
            }

            .nav,
            .section,
            .footer-inner {
                width: min(1120px, calc(100% - 40px));
                margin: 0 auto;
            }

            .nav {
                min-height: 72px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 800;
                color: #111827;
            }

            .brand-mark {
                width: 42px;
                height: 42px;
                display: grid;
                place-items: center;
                border-radius: 8px;
                background: #f59e0b;
                color: #111827;
                font-weight: 900;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 16px;
                color: #475467;
                font-size: 0.95rem;
                font-weight: 600;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 44px;
                padding: 0 18px;
                border-radius: 8px;
                background: #111827;
                color: #ffffff;
                font-weight: 700;
            }

            .hero {
                background: linear-gradient(135deg, #ffffff 0%, #f5f7f8 55%, #fff7e6 100%);
                border-bottom: 1px solid #e2e8f0;
            }

            .hero .section {
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
                align-items: center;
                gap: clamp(32px, 6vw, 80px);
                padding: clamp(56px, 9vw, 112px) 0;
            }

            .eyebrow {
                margin: 0 0 14px;
                color: #b7791f;
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                max-width: 720px;
                color: #111827;
                font-size: clamp(2.4rem, 6vw, 4.7rem);
                line-height: 1;
                letter-spacing: 0;
            }

            .lead {
                max-width: 620px;
                margin: 24px 0 0;
                color: #475467;
                font-size: clamp(1.05rem, 2vw, 1.25rem);
                line-height: 1.7;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 32px;
            }

            .button.secondary {
                background: #ffffff;
                color: #111827;
                border: 1px solid #d0d5dd;
            }

            .dashboard-preview {
                border: 1px solid #d8dee7;
                border-radius: 8px;
                background: #ffffff;
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
                overflow: hidden;
            }

            .preview-bar {
                height: 46px;
                display: flex;
                align-items: center;
                gap: 7px;
                padding: 0 16px;
                border-bottom: 1px solid #e2e8f0;
                background: #f8fafc;
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #d0d5dd;
            }

            .preview-body {
                padding: 20px;
            }

            .metric-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .metric {
                min-height: 92px;
                padding: 16px;
                border: 1px solid #edf0f4;
                border-radius: 8px;
                background: #fbfcfd;
            }

            .metric span {
                display: block;
                color: #667085;
                font-size: 0.82rem;
                font-weight: 700;
            }

            .metric strong {
                display: block;
                margin-top: 12px;
                color: #111827;
                font-size: 1.55rem;
            }

            .chart {
                height: 130px;
                margin-top: 14px;
                border-radius: 8px;
                background:
                    linear-gradient(180deg, rgba(245, 158, 11, 0.16), rgba(245, 158, 11, 0)),
                    repeating-linear-gradient(90deg, transparent 0 46px, rgba(17, 24, 39, 0.06) 46px 47px),
                    linear-gradient(135deg, transparent 20%, rgba(245, 158, 11, 0.9) 20% 23%, transparent 23% 45%, rgba(17, 24, 39, 0.78) 45% 48%, transparent 48% 68%, rgba(245, 158, 11, 0.9) 68% 71%, transparent 71%);
                border: 1px solid #edf0f4;
            }

            .features {
                padding: 64px 0;
                background: #ffffff;
            }

            .section-title {
                margin: 0 0 28px;
                color: #111827;
                font-size: clamp(1.75rem, 3vw, 2.4rem);
                line-height: 1.15;
            }

            .feature-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .feature {
                padding: 22px;
                border: 1px solid #e4e7eb;
                border-radius: 8px;
                background: #ffffff;
            }

            .feature h2 {
                margin: 0 0 10px;
                color: #111827;
                font-size: 1.08rem;
            }

            .feature p {
                margin: 0;
                color: #667085;
                line-height: 1.65;
            }

            .footer {
                margin-top: auto;
                border-top: 1px solid #e2e8f0;
                background: #111827;
                color: #d0d5dd;
            }

            .footer-inner {
                min-height: 86px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                font-size: 0.95rem;
            }

            .footer a {
                color: #fbbf24;
                font-weight: 700;
            }

            @media (max-width: 840px) {
                .hero .section,
                .feature-grid {
                    grid-template-columns: 1fr;
                }

                .nav {
                    align-items: flex-start;
                    flex-direction: column;
                    padding: 18px 0;
                }

                .nav-links {
                    width: 100%;
                    justify-content: space-between;
                }
            }

            @media (max-width: 560px) {
                .nav,
                .section,
                .footer-inner {
                    width: min(100% - 28px, 1120px);
                }

                .nav-links,
                .actions,
                .footer-inner {
                    flex-direction: column;
                    align-items: stretch;
                }

                .button {
                    width: 100%;
                }

                .metric-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="header">
                <nav class="nav" aria-label="Primary navigation">
                    <a class="brand" href="{{ url('/') }}">
                        <span class="brand-mark">MRF</span>
                        <span>{{ config('app.name', 'MRF Showroom Admin') }}</span>
                    </a>

                    <div class="nav-links">
                        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                        <a class="button" href="{{ url('/admin') }}">Admin Login</a>
                    </div>
                </nav>
            </header>

            <main>
                <section class="hero">
                    <div class="section">
                        <div>
                            <p class="eyebrow">Showroom operations, organized</p>
                            <h1>Manage stock, sales, invoices, and reports from one place.</h1>
                            <p class="lead">
                                A focused admin system for tracking tyre inventory, showroom movement,
                                customer invoices, debts, attendance, and daily sales activity.
                            </p>

                            <div class="actions">
                                <a class="button" href="{{ url('/admin') }}">Open Admin Panel</a>
                                <a class="button secondary" href="{{ route('privacy-policy') }}">View Privacy Policy</a>
                            </div>
                        </div>

                        <div class="dashboard-preview" aria-label="Dashboard preview">
                            <div class="preview-bar">
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </div>

                            <div class="preview-body">
                                <div class="metric-grid">
                                    <div class="metric">
                                        <span>Total Stock</span>
                                        <strong>12,480</strong>
                                    </div>
                                    <div class="metric">
                                        <span>Monthly Sales</span>
                                        <strong>836</strong>
                                    </div>
                                    <div class="metric">
                                        <span>Showrooms</span>
                                        <strong>8</strong>
                                    </div>
                                    <div class="metric">
                                        <span>Pending Debt</span>
                                        <strong>42</strong>
                                    </div>
                                </div>

                                <div class="chart" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="features">
                    <div class="section">
                        <h2 class="section-title">Built for daily showroom work</h2>

                        <div class="feature-grid">
                            <article class="feature">
                                <h2>Inventory Control</h2>
                                <p>Track tyre stock, transfers, low-stock alerts, and showroom availability.</p>
                            </article>

                            <article class="feature">
                                <h2>Sales Reporting</h2>
                                <p>Review daily and monthly sales performance with clear operational reports.</p>
                            </article>

                            <article class="feature">
                                <h2>Invoices and Debt</h2>
                                <p>Manage invoices, customer balances, and payment follow-up from the admin panel.</p>
                            </article>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="footer">
                <div class="footer-inner">
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'MRF Showroom Admin') }}. All rights reserved.</span>
                    <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                </div>
            </footer>
        </div>
    </body>
</html>
