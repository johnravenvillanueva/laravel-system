
@extends('layouts.main')

@section('content')
<style>
    :root {
        --bg-dark: #1b1a1d;
        --bg-panel: #2a282a;
        --bg-header: #2f2c2d;
        --bg-table: #f3f1ef;
        --border-soft: #c9c4c0;
        --text-main: #2d2725;
        --text-soft: #766d69;
        --accent: #7a5d4d;
        --accent-dark: #533f35;
        --chip-green: #dff6e8;
        --chip-text: #2e6b4f;
        --danger: #db4d3b;
        --danger-dark: #a93a2e;
        --white: #f5f3f1;
        --shadow: rgba(17, 17, 19, 0.2);
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        background: var(--bg-dark);
        color: var(--text-main);
        font-family: Georgia, 'Times New Roman', serif;
    }

    .inventory-shell {
        min-height: 100vh;
        background: #1e1d1f;
        padding: 0;
    }

    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2rem 2.6rem 1.2rem;
        background: rgba(28, 27, 29, 0.98);
    }

    .brand-wrap {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .brand-mark {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d6b89b, #8b694f);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3d2d26;
        font-size: 1.6rem;
        font-weight: 700;
        box-shadow: 0 0 0 3px rgba(255,255,255,0.08);
    }

    .brand-name {
        font-size: 2.6rem;
        letter-spacing: -0.06em;
        font-weight: 700;
        color: #f6f2ee;
        margin: 0;
    }

    .user-area {
        display: flex;
        align-items: center;
        gap: 1.4rem;
        color: #e7e2df;
        font-size: 1.1rem;
        font-family: Arial, sans-serif;
    }

    .logout-btn {
        background: #b18968;
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 1.2rem;
        padding: 0.9rem 1.7rem;
        cursor: pointer;
        font-weight: 600;
    }

    .inventory-panel {
        background: #efeae6;
        margin: 0 0 0;
        min-height: calc(100vh - 170px);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);
    }

    .tabs {
        display: grid;
        grid-template-columns: repeat(5, minmax(120px, 1fr));
        background: #4a4443;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    .tab {
        background: rgba(255,255,255,0.03);
        color: #f3efee;
        border: none;
        padding: 1.1rem 1rem;
        font-size: 1.1rem;
        font-family: Arial, sans-serif;
        cursor: pointer;
        position: relative;
    }

    .tab.active {
        background: #706b69;
        color: #fff;
    }

    .tab.active::after {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 3px;
        background: rgba(255,255,255,0.8);
    }

    .content-wrap {
        padding: 2.2rem 2.6rem 1.4rem;
    }

    .title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.3rem;
    }

    .title-row h1 {
        margin: 0;
        font-size: 3.4rem;
        font-weight: 700;
        letter-spacing: -0.06em;
        color: #2a2423;
    }

    .title-row h1 span {
        font-weight: 700;
    }

    .header-icons {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .profile-badge {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        border: 3px solid #2d2927;
        background: #f1efe8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2d2927;
        font-size: 2.2rem;
        font-weight: 700;
    }

    .bell-icon {
        width: 56px;
        height: 56px;
        border: 3px solid #2d2927;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1efe8;
        font-size: 2.1rem;
        color: #2f2c2b;
    }

    .tool-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1rem 0 1.5rem;
    }

    .primary-btn, .secondary-btn {
        border: none;
        border-radius: 10px;
        padding: 0.8rem 1.1rem;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
    }

    .primary-btn {
        background: var(--accent);
        color: #fff;
    }

    .secondary-btn {
        background: #5f504a;
        color: #fff;
        opacity: 0.9;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: rgba(255,255,255,0.22);
        font-family: Arial, sans-serif;
    }

    th {
        text-align: left;
        font-size: 0.8rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #5a4e4b;
        padding: 0.9rem 0.7rem;
        border-bottom: 2px solid rgba(72, 60, 56, 0.45);
        font-weight: 700;
    }

    td {
        padding: 1.1rem 0.7rem;
        border-bottom: 1px solid rgba(72, 60, 56, 0.25);
        color: #3b2f2a;
        font-size: 1.05rem;
        vertical-align: middle;
    }

    .status-pill {
        display: inline-block;
        background: var(--chip-green);
        color: var(--chip-text);
        border-radius: 999px;
        padding: 0.34rem 0.8rem;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .action-col {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-start;
    }

    .edit-btn, .delete-btn {
        border: none;
        border-radius: 8px;
        color: #fff;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
    }

    .edit-btn {
        background: #b97a5d;
    }

    .delete-btn {
        background: #d34f3d;
    }

    .footer-note {
        text-align: center;
        padding: 2rem 1rem 2.5rem;
        color: #eae5e2;
        font-size: 1rem;
        font-family: Arial, sans-serif;
    }

    @media (max-width: 900px) {
        .tabs {
            grid-template-columns: repeat(2, minmax(140px, 1fr));
        }

        .title-row {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .title-row h1 {
            font-size: 2.4rem;
        }

        .content-wrap {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        table {
            display: block;
            overflow-x: auto;
        }
    }
</style>

<div class="inventory-shell">
    <header class="topbar">
        <div class="brand-wrap">
            <div class="brand-mark">I</div>
            <h1 class="brand-name">Issang Macchiatos</h1>
        </div>

        <div class="user-area">
            <span>Welcome, {{ auth()->user()->name ?? 'Administrator' }}!</span>
            <button class="logout-btn" type="button">Logout</button>
        </div>
    </header>

    <div class="inventory-panel">
        <nav class="tabs" aria-label="Inventory sections">
            <button class="tab active" type="button">Dashboard</button>
            <button class="tab" type="button">Sales</button>
            <button class="tab" type="button">Orders</button>
            <button class="tab" type="button">Inventory</button>
            <button class="tab" type="button">Reports</button>
        </nav>

        <div class="content-wrap">
            <div class="title-row">
                <h1>Inventory <span>Management</span></h1>
                <div class="header-icons">
                    <div class="profile-badge">👤</div>
                    <div class="bell-icon">🔔</div>
                </div>
            </div>

            <div class="tool-row">
                <button class="primary-btn" type="button">+ Add Item</button>
                <button class="secondary-btn" type="button">Manage Products</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Min. Threshold</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['category'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['unit'] }}</td>
                            <td>{{ $item['min_threshold'] }}</td>
                            <td><span class="status-pill">{{ $item['status'] }}</span></td>
                            <td>
                                <div class="action-col">
                                    <button class="edit-btn" type="button">Edit</button>
                                    <button class="delete-btn" type="button">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-note">
        © 2026 Issang Macchiatos - Pinaod, San Ildefonso, Bulacan<br>
        Facebook Page : Issang Macchiatos - Pinaod
    </div>
</div>
@endsection
