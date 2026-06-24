<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Privacy Policy | {{ config('app.name', 'MRF Stock') }}</title>

        <style>
            :root {
                color-scheme: light;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: #1f2933;
                background: #f7f8fa;
            }

            body {
                margin: 0;
            }

            main {
                max-width: 860px;
                margin: 0 auto;
                padding: 48px 20px 64px;
            }

            .policy {
                background: #ffffff;
                border: 1px solid #e4e7eb;
                border-radius: 8px;
                padding: clamp(24px, 5vw, 48px);
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }

            h1 {
                margin: 0 0 8px;
                color: #111827;
                font-size: clamp(2rem, 5vw, 3rem);
                line-height: 1.1;
            }

            h2 {
                margin: 32px 0 10px;
                color: #111827;
                font-size: 1.2rem;
            }

            p,
            li {
                font-size: 1rem;
                line-height: 1.75;
            }

            p {
                margin: 0 0 16px;
            }

            ul {
                margin: 0 0 16px;
                padding-left: 22px;
            }

            .updated {
                color: #667085;
                font-size: 0.95rem;
            }

            a {
                color: #b7791f;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <main>
            <article class="policy">
                <h1>Privacy Policy</h1>
                <p class="updated">Last updated: June 24, 2026</p>

                <p>
                    This Privacy Policy explains how {{ config('app.name', 'MRF Stock') }} collects,
                    uses, and protects information when you use our stock management services.
                </p>

                <h2>Information We Collect</h2>
                <p>
                    We may collect account details, showroom and inventory records, sales information,
                    attendance records, device details, and other information needed to operate the
                    service.
                </p>

                <h2>How We Use Information</h2>
                <ul>
                    <li>To provide, maintain, and improve the stock management system.</li>
                    <li>To authenticate users and protect accounts from unauthorized access.</li>
                    <li>To prepare reports, track business activity, and support operational workflows.</li>
                    <li>To communicate important service, security, or administrative updates.</li>
                </ul>

                <h2>Data Sharing</h2>
                <p>
                    We do not sell personal information. We may share information only with authorized
                    users, service providers who help operate the system, or when required by law.
                </p>

                <h2>Data Security</h2>
                <p>
                    We use reasonable technical and organizational safeguards to protect information.
                    No method of transmission or storage is completely secure, so we continually review
                    and improve our practices.
                </p>

                <h2>Data Retention</h2>
                <p>
                    We retain information for as long as needed to provide the service, meet business
                    requirements, resolve disputes, and comply with legal obligations.
                </p>

                <h2>Your Choices</h2>
                <p>
                    You may request access, correction, or deletion of your information where applicable.
                    Some records may need to be retained for legitimate business or legal reasons.
                </p>

                <h2>Contact</h2>
                <p>
                    For privacy questions or requests, please contact the administrator of this service.
                </p>
            </article>
        </main>
    </body>
</html>
