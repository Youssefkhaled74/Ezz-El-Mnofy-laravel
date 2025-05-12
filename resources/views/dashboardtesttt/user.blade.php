<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #fdfcfc;
            color: #333;
            line-height: 1.6;
        }

        /* Navigation Bar Styles */
        nav {
            background-color: #c8102e;
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        nav ul li {
            margin: 0 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            padding: 8px 16px;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #a10d24;
            border-radius: 6px;
        }

        h1 {
            text-align: center;
            color: #c8102e;
            margin: 40px 0 30px;
            font-size: 32px;
            font-weight: 700;
        }

        table {
            width: 90%;
            margin: 0 auto 40px;
            border-collapse: collapse;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background-color: #c8102e;
            color: #fff;
        }

        thead th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }

        tbody td {
            padding: 16px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #fff1f2;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            nav ul {
                flex-direction: column;
                text-align: center;
            }

            nav ul li {
                margin: 10px 0;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 20px;
                border-bottom: 2px solid #c8102e;
                padding: 12px;
                border-radius: 8px;
            }

            tbody td {
                padding: 12px 16px;
                position: relative;
                font-size: 14px;
            }

            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 16px;
                top: 12px;
                font-weight: 600;
                color: #c8102e;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="#peking">Return to Peking</a></li>
            <li><a href="{{ route('dashboard.order_ratings') }}">See the Order Rating</a></li>        </ul>
    </nav>

    <h1>All Users</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Birthday</th>
                <th>My Referral Code</th>
                <th>Referral Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td data-label="Name">{{ $user->name }}</td>
                    <td data-label="Birthday">{{ $user->birthday }}</td>
                    <td data-label="My Referral Code">{{ $user->my_referral_code }}</td>
                    <td data-label="Referral Code">{{ $user->referral_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>