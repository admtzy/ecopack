<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EcoPack' }}</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #15803d;
            --primary-hover: #166534;
            --primary-light: #f0fdf4;
            --bg-body: #f8faf9;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --card-bg: #ffffff;
            --radius: 10px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header / Navbar */
        header {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 5%;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            height: 64px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav a {
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav a:hover {
            color: var(--primary);
        }

        /* Layout Main Content */
        main {
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 32px auto;
            padding: 0 20px;
        }

        /* Card Component */
        .card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        /* Forms & Inputs */
        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            margin: 6px 0 16px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.15);
        }

        /* Buttons */
        button, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            padding: 10px 18px;
            border: 0;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        button:hover, .btn:hover {
            background: var(--primary-hover);
        }

        button:active, .btn:active {
            transform: scale(0.98);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary-light);
        }

        /* Table Design */
        table {
            border-collapse: collapse;
            width: 100%;
            background: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Alerts / Messages */
        .alert {
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert ul {
            margin-left: 20px;
        }

        /* Utilities */
        .muted {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <header>
        <div class="navbar">
            <a href="/" class="brand">
                🌱 EcoPack
            </a>

            <nav>
                @auth 
                    <a href="{{ route('dashboard') }}">Dashboard</a> 

                    @if(auth()->user()->role === 'admin') 
                        <a href="{{ route('dpp.index') }}">DPP</a>
                        <a href="{{ route('admin.redemptions') }}">Penukaran</a>
                        <a href="{{ route('partnerships.index') }}">Mitra</a> 
                    @elseif(auth()->user()->role === 'user') 
                        <a href="{{ route('rewards.index') }}">Reward</a> 
                    @else 
                        <a href="{{ route('partnerships.create') }}">Ajukan Mitra</a> 
                    @endif 

                    <form style="display:inline" method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-outline">Keluar</button>
                    </form> 
                @else 
                    <a href="{{ route('login') }}" class="btn">Login</a> 
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif 

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        @yield('content')
    </main>

</body>
</html>