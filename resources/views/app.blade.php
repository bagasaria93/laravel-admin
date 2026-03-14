<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95' }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
        #page-dashboard { display: none; width: 100%; min-height: 100vh; }
        #page-dashboard.is-active { display: flex; }
        .sidebar { width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all .15s; color: #c4b5fd; font-size: 14px; font-weight: 500; }
        .nav-item:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,.15); color: #fff; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border: 1px solid #f3f4f6; }
        .toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; color: #fff; font-size: 14px; font-weight: 500; z-index: 9999; transform: translateY(80px); opacity: 0; transition: all .3s ease; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { background: #059669; }
        .toast.error { background: #dc2626; }
        .badge-active { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-inactive { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .btn-primary { background: #5b21b6; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary:hover { background: #6d28d9; }
        .btn-secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: background .15s; }
        .btn-secondary:hover { background: #f9fafb; }
        input, select, textarea { outline: none; transition: border-color .15s; }
        input:focus, select:focus, textarea:focus { border-color: #7c3aed !important; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
        table { border-collapse: collapse; width: 100%; }
        th { background: #f9fafb; padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        th.sortable { cursor: pointer; user-select: none; }
        th.sortable:hover { background: #f3f4f6; color: #4b5563; }
        th.sort-asc .sort-icon::after { content: ' \2191'; }
        th.sort-desc .sort-icon::after { content: ' \2193'; }
        th .sort-icon { color: #a78bfa; }
        td { padding: 14px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #faf5ff; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(2px); }
        .spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .search-input { padding: 8px 12px 8px 36px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; width: 240px; }
        .search-wrapper { position: relative; }
        .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50">

<div id="toast" class="toast"></div>

<div id="page-login" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
    <div class="w-full max-w-md px-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Laravel Admin</h1>
            <p class="text-primary-200 mt-1 text-sm">Product Catalog Management System</p>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Welcome back</h2>
            <p class="text-sm text-gray-500 mb-6">Sign in to your admin account</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="login-email" type="email" value="admin@example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="login-password" type="password" value="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
                <div id="login-error" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg"></div>
                <button id="btn-login" class="w-full py-2.5 bg-primary-700 text-white rounded-lg font-semibold text-sm hover:bg-primary-800 transition-colors flex items-center justify-center gap-2">Sign In</button>
            </div>
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Demo Credentials</p>
                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between"><span class="font-medium">Admin:</span><span>admin@example.com / password</span></div>
                    <div class="flex justify-between"><span class="font-medium">User:</span><span>user@example.com / password</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="page-dashboard">
    <div class="sidebar bg-primary-900 flex flex-col">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-sm">LA</span>
                </div>
                <div>
                    <div class="text-white font-bold text-sm">Laravel Admin</div>
                    <div class="text-primary-300 text-xs">v1.0.0</div>
                </div>
            </div>
        </div>
        <div class="p-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-primary-700 rounded-full flex items-center justify-center">
                    <span id="user-avatar" class="text-white font-bold text-sm">A</span>
                </div>
                <div>
                    <div id="sidebar-username" class="text-white text-sm font-semibold"></div>
                    <div id="sidebar-role" class="text-primary-300 text-xs capitalize"></div>
                </div>
            </div>
        </div>
        <nav class="p-4 flex-1 space-y-1">
            <div id="nav-dashboard" class="nav-item active" onclick="switchPage('dashboard')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Dashboard
            </div>
            <div id="nav-products" class="nav-item" onclick="switchPage('products')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                Products
            </div>
            <div id="nav-categories" class="nav-item" onclick="switchPage('categories')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                Categories
            </div>
            <div id="nav-api" class="nav-item" onclick="switchPage('api')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                API Docs
            </div>
        </nav>
        <div class="p-4 border-t border-white/10">
            <button id="btn-logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-300 hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </div>
    </div>

    <div class="main-content">
        <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 id="page-title" class="text-xl font-bold text-gray-800"></h1>
                <p id="page-subtitle" class="text-sm text-gray-500"></p>
            </div>
            <div id="topbar-badge" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-bold uppercase"></div>
        </div>

        <div class="flex-1">

            <div id="section-dashboard" class="p-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-500">Total Products</span>
                            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                            </div>
                        </div>
                        <div id="stat-products" class="text-3xl font-bold text-gray-800">-</div>
                        <div class="text-xs text-gray-400 mt-1">Active products in catalog</div>
                    </div>
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-500">Categories</span>
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                        </div>
                        <div id="stat-categories" class="text-3xl font-bold text-gray-800">-</div>
                        <div class="text-xs text-gray-400 mt-1">Product categories</div>
                    </div>
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-500">Total Stock</span>
                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                        </div>
                        <div id="stat-stock" class="text-3xl font-bold text-gray-800">-</div>
                        <div class="text-xs text-gray-400 mt-1">Units across all products</div>
                    </div>
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-500">Inventory Value</span>
                            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div id="stat-value" class="text-2xl font-bold text-gray-800">-</div>
                        <div class="text-xs text-gray-400 mt-1">Total inventory value</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Recent Products</h3>
                        <button class="text-sm text-primary-600 font-medium hover:text-primary-700" onclick="switchPage('products')">View all</button>
                    </div>
                    <table>
                        <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
                        <tbody id="recent-products-table"></tbody>
                    </table>
                </div>
            </div>

            <div id="section-products" class="p-8 hidden">
                <div class="flex items-center justify-between mb-6">
                    <div class="search-wrapper">
                        <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="search-products" type="text" placeholder="Search products..." class="search-input">
                    </div>
                    <button id="btn-add-product" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Product
                    </button>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable" data-col="name" data-table="products">Name <span class="sort-icon"></span></th>
                                <th class="sortable" data-col="category" data-table="products">Category <span class="sort-icon"></span></th>
                                <th class="sortable" data-col="price" data-table="products">Price <span class="sort-icon"></span></th>
                                <th class="sortable" data-col="stock" data-table="products">Stock <span class="sort-icon"></span></th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-table"></tbody>
                    </table>
                    <div id="products-empty" class="empty-state hidden">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                        <p class="text-sm">No products found</p>
                    </div>
                </div>
            </div>

            <div id="section-categories" class="p-8 hidden">
                <div class="flex items-center justify-between mb-6">
                    <div class="search-wrapper">
                        <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="search-categories" type="text" placeholder="Search categories..." class="search-input">
                    </div>
                    <button id="btn-add-category" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Category
                    </button>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable" data-col="name" data-table="categories">Name <span class="sort-icon"></span></th>
                                <th class="sortable" data-col="slug" data-table="categories">Slug <span class="sort-icon"></span></th>
                                <th class="sortable" data-col="products_count" data-table="categories">Products <span class="sort-icon"></span></th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categories-table"></tbody>
                    </table>
                    <div id="categories-empty" class="empty-state hidden">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        <p class="text-sm">No categories found</p>
                    </div>
                </div>
            </div>

            <div id="section-api" class="p-8 hidden">
                <div class="max-w-3xl space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-1">Authentication Header</h3>
                        <p class="text-sm text-gray-500 mb-3">Include this header in all protected API requests.</p>
                        <code class="block bg-gray-900 text-green-400 px-4 py-3 rounded-lg text-sm font-mono">Authorization: Bearer {your_token}</code>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Your Current Token</h3>
                        <div id="token-display" class="bg-gray-900 text-green-400 px-4 py-3 rounded-lg text-xs font-mono break-all"></div>
                        <button id="btn-copy-token" class="mt-3 btn-secondary text-xs flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy Token
                        </button>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Authentication Endpoints</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded font-bold text-xs min-w-12 text-center">POST</span><code class="text-gray-700 font-mono">/api/login</code><span class="text-gray-400 ml-auto text-xs">Get token</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded font-bold text-xs min-w-12 text-center">POST</span><code class="text-gray-700 font-mono">/api/register</code><span class="text-gray-400 ml-auto text-xs">Register user</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded font-bold text-xs min-w-12 text-center">POST</span><code class="text-gray-700 font-mono">/api/logout</code><span class="text-gray-400 ml-auto text-xs">Revoke token</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-sky-100 text-sky-700 rounded font-bold text-xs min-w-12 text-center">GET</span><code class="text-gray-700 font-mono">/api/me</code><span class="text-gray-400 ml-auto text-xs">Current user</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Products Endpoints</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-sky-100 text-sky-700 rounded font-bold text-xs min-w-12 text-center">GET</span><code class="text-gray-700 font-mono">/api/products</code><span class="text-gray-400 ml-auto text-xs">List all</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-sky-100 text-sky-700 rounded font-bold text-xs min-w-12 text-center">GET</span><code class="text-gray-700 font-mono">/api/products/{id}</code><span class="text-gray-400 ml-auto text-xs">Get detail</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded font-bold text-xs min-w-12 text-center">POST</span><code class="text-gray-700 font-mono">/api/products</code><span class="text-gray-400 ml-auto text-xs">Create (admin)</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded font-bold text-xs min-w-12 text-center">PUT</span><code class="text-gray-700 font-mono">/api/products/{id}</code><span class="text-gray-400 ml-auto text-xs">Update (admin)</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded font-bold text-xs min-w-12 text-center">DEL</span><code class="text-gray-700 font-mono">/api/products/{id}</code><span class="text-gray-400 ml-auto text-xs">Delete (admin)</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Categories Endpoints</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-sky-100 text-sky-700 rounded font-bold text-xs min-w-12 text-center">GET</span><code class="text-gray-700 font-mono">/api/categories</code><span class="text-gray-400 ml-auto text-xs">List all</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-sky-100 text-sky-700 rounded font-bold text-xs min-w-12 text-center">GET</span><code class="text-gray-700 font-mono">/api/categories/{id}</code><span class="text-gray-400 ml-auto text-xs">Get detail</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded font-bold text-xs min-w-12 text-center">POST</span><code class="text-gray-700 font-mono">/api/categories</code><span class="text-gray-400 ml-auto text-xs">Create (admin)</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded font-bold text-xs min-w-12 text-center">PUT</span><code class="text-gray-700 font-mono">/api/categories/{id}</code><span class="text-gray-400 ml-auto text-xs">Update (admin)</span></div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg text-sm"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded font-bold text-xs min-w-12 text-center">DEL</span><code class="text-gray-700 font-mono">/api/categories/{id}</code><span class="text-gray-400 ml-auto text-xs">Delete (admin)</span></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <footer class="bg-white border-t border-gray-200 px-8 py-4 mt-auto">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Built with <span class="text-red-400"></span> by
                    <a href="https://bagasaria93.github.io" target="_blank" class="text-primary-700 font-semibold hover:text-primary-800">Bagas Aria Sativa</a>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-emerald-400 rounded-full inline-block"></span>Laravel 11</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-blue-400 rounded-full inline-block"></span>Sanctum Auth</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-orange-400 rounded-full inline-block"></span>REST API</span>
                    <a href="https://github.com/bagasaria93/laravel-admin" target="_blank" class="flex items-center gap-1.5 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                        Source Code
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>

<div id="modal" class="modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h3 id="modal-title" class="text-lg font-bold text-gray-800"></h3>
            <button id="btn-close-modal" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modal-body" class="px-6 py-5 space-y-4"></div>
        <div class="flex gap-3 px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-100">
            <button id="btn-cancel-modal" class="btn-secondary flex-1">Cancel</button>
            <button id="modal-submit" class="btn-primary flex-1 flex items-center justify-center gap-2">Save</button>
        </div>
    </div>
</div>

<div id="confirm-modal" class="modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-800 text-center mb-2">Confirm Delete</h3>
        <p id="confirm-text" class="text-sm text-gray-500 text-center mb-6"></p>
        <div class="flex gap-3">
            <button id="btn-confirm-cancel" class="btn-secondary flex-1">Cancel</button>
            <button id="btn-confirm-ok" class="flex-1 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">Delete</button>
        </div>
    </div>
</div>

<script>
    let token = localStorage.getItem('token');
    let currentUser = null;
    let editingId = null;
    let modalType = null;
    let categories = [];
    let allProducts = [];
    let allCategories = [];
    let sortState = { products: { col: null, dir: 'asc' }, categories: { col: null, dir: 'asc' } };

    async function apiFetch(url, options = {}) {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (token) headers['Authorization'] = 'Bearer ' + token;
        const res = await fetch(url, { ...options, headers });
        return res.json();
    }

    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = 'toast ' + type;
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    async function login() {
        const btn = document.getElementById('btn-login');
        btn.innerHTML = '<div class="spinner"></div> Signing in...';
        btn.disabled = true;
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const err = document.getElementById('login-error');
        err.classList.add('hidden');
        const data = await apiFetch('/api/login', { method: 'POST', body: JSON.stringify({ email, password }) });
        if (data.token) {
            token = data.token;
            currentUser = data.user;
            localStorage.setItem('token', token);
            showDashboard();
        } else {
            err.textContent = 'Invalid email or password.';
            err.classList.remove('hidden');
            btn.innerHTML = 'Sign In';
            btn.disabled = false;
        }
    }

    async function logout() {
        await apiFetch('/api/logout', { method: 'POST' });
        token = null;
        currentUser = null;
        localStorage.removeItem('token');
        document.getElementById('page-login').style.display = '';
        document.getElementById('page-dashboard').classList.remove('is-active');
        showToast('Signed out successfully', 'success');
    }

    async function showDashboard() {
        document.getElementById('page-login').style.display = 'none';
        document.getElementById('page-dashboard').classList.add('is-active');
        document.getElementById('sidebar-username').textContent = currentUser.name;
        document.getElementById('sidebar-role').textContent = currentUser.role;
        document.getElementById('user-avatar').textContent = currentUser.name.charAt(0).toUpperCase();
        document.getElementById('topbar-badge').textContent = currentUser.role;
        document.getElementById('token-display').textContent = token;
        await loadProducts();
        await loadCategories();
        updateStats();
        switchPage('dashboard');
    }

    function switchPage(page) {
        ['dashboard','products','categories','api'].forEach(p => {
            document.getElementById('section-' + p).classList.add('hidden');
            const nav = document.getElementById('nav-' + p);
            if (nav) nav.classList.remove('active');
        });
        document.getElementById('section-' + page).classList.remove('hidden');
        const nav = document.getElementById('nav-' + page);
        if (nav) nav.classList.add('active');
        const titles = {
            dashboard: ['Dashboard', 'Overview of your product catalog'],
            products: ['Products', 'Manage your product listings'],
            categories: ['Categories', 'Manage product categories'],
            api: ['API Documentation', 'Available REST API endpoints']
        };
        document.getElementById('page-title').textContent = titles[page][0];
        document.getElementById('page-subtitle').textContent = titles[page][1];
        if (page === 'dashboard') renderRecentProducts();
    }

    function updateStats() {
        document.getElementById('stat-products').textContent = allProducts.length;
        document.getElementById('stat-categories').textContent = allCategories.length;
        const totalStock = allProducts.reduce((s, p) => s + Number(p.stock), 0);
        document.getElementById('stat-stock').textContent = totalStock.toLocaleString('id-ID');
        const totalValue = allProducts.reduce((s, p) => s + (Number(p.price) * Number(p.stock)), 0);
        document.getElementById('stat-value').textContent = 'Rp ' + totalValue.toLocaleString('id-ID');
    }

    function renderRecentProducts() {
        const tbody = document.getElementById('recent-products-table');
        tbody.innerHTML = '';
        allProducts.slice(0, 5).forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="font-medium text-gray-800">${p.name}</td><td class="text-gray-500">${p.category ? p.category.name : '-'}</td><td class="text-gray-600">Rp ${Number(p.price).toLocaleString('id-ID')}</td><td class="text-gray-600">${p.stock}</td><td><span class="${p.is_active ? 'badge-active' : 'badge-inactive'}">${p.is_active ? 'Active' : 'Inactive'}</span></td>`;
            tbody.appendChild(tr);
        });
    }

    async function loadProducts() {
        const data = await apiFetch('/api/products');
        allProducts = Array.isArray(data) ? data : [];
        renderProducts(allProducts);
    }

    function sortData(list, col, dir, type) {
        return [...list].sort((a, b) => {
            let va, vb;
            if (type === 'products') {
                if (col === 'name') { va = a.name.toLowerCase(); vb = b.name.toLowerCase(); }
                else if (col === 'category') { va = (a.category ? a.category.name : '').toLowerCase(); vb = (b.category ? b.category.name : '').toLowerCase(); }
                else if (col === 'price') { va = Number(a.price); vb = Number(b.price); }
                else if (col === 'stock') { va = Number(a.stock); vb = Number(b.stock); }
            } else {
                if (col === 'name') { va = a.name.toLowerCase(); vb = b.name.toLowerCase(); }
                else if (col === 'slug') { va = a.slug.toLowerCase(); vb = b.slug.toLowerCase(); }
                else if (col === 'products_count') { va = Number(a.products_count ?? 0); vb = Number(b.products_count ?? 0); }
            }
            if (va < vb) return dir === 'asc' ? -1 : 1;
            if (va > vb) return dir === 'asc' ? 1 : -1;
            return 0;
        });
    }

    function updateSortHeaders(tableType) {
        const { col, dir } = sortState[tableType];
        document.querySelectorAll(`th[data-table="${tableType}"]`).forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
            if (th.dataset.col === col) th.classList.add(dir === 'asc' ? 'sort-asc' : 'sort-desc');
        });
    }

    function renderProducts(list) {
        const tbody = document.getElementById('products-table');
        const empty = document.getElementById('products-empty');
        tbody.innerHTML = '';
        if (!list.length) { empty.classList.remove('hidden'); return; }
        empty.classList.add('hidden');
        list.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="font-medium text-gray-800">${p.name}${p.description ? '<div class="text-xs text-gray-400 mt-0.5 truncate max-w-48">' + p.description + '</div>' : ''}</td><td><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-medium">${p.category ? p.category.name : '-'}</span></td><td class="font-semibold text-gray-800">Rp ${Number(p.price).toLocaleString('id-ID')}</td><td>${p.stock}</td><td><span class="${p.is_active ? 'badge-active' : 'badge-inactive'}">${p.is_active ? 'Active' : 'Inactive'}</span></td><td><div class="flex gap-3"><button class="text-primary-600 hover:text-primary-800 text-xs font-semibold btn-edit-product" data-id="${p.id}">Edit</button><button class="text-red-500 hover:text-red-700 text-xs font-semibold btn-del-product" data-id="${p.id}" data-name="${p.name}">Delete</button></div></td>`;
            tbody.appendChild(tr);
        });
        document.querySelectorAll('.btn-edit-product').forEach(b => b.addEventListener('click', () => editProduct(b.dataset.id)));
        document.querySelectorAll('.btn-del-product').forEach(b => b.addEventListener('click', () => confirmDelete('product', b.dataset.id, b.dataset.name)));
    }

    async function loadCategories() {
        const data = await apiFetch('/api/categories');
        allCategories = Array.isArray(data) ? data : [];
        categories = allCategories;
        renderCategories(allCategories);
    }

    function renderCategories(list) {
        const tbody = document.getElementById('categories-table');
        const empty = document.getElementById('categories-empty');
        tbody.innerHTML = '';
        if (!list.length) { empty.classList.remove('hidden'); return; }
        empty.classList.add('hidden');
        list.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="font-medium text-gray-800">${c.name}</td><td><code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">${c.slug}</code></td><td><span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-xs font-semibold">${c.products_count ?? 0} products</span></td><td class="text-gray-500 text-xs max-w-48 truncate">${c.description ?? '-'}</td><td><div class="flex gap-3"><button class="text-primary-600 hover:text-primary-800 text-xs font-semibold btn-edit-cat" data-id="${c.id}">Edit</button><button class="text-red-500 hover:text-red-700 text-xs font-semibold btn-del-cat" data-id="${c.id}" data-name="${c.name}">Delete</button></div></td>`;
            tbody.appendChild(tr);
        });
        document.querySelectorAll('.btn-edit-cat').forEach(b => b.addEventListener('click', () => editCategory(b.dataset.id)));
        document.querySelectorAll('.btn-del-cat').forEach(b => b.addEventListener('click', () => confirmDelete('category', b.dataset.id, b.dataset.name)));
    }

    function confirmDelete(type, id, name) {
        document.getElementById('confirm-text').textContent = 'Are you sure you want to delete "' + name + '"? This action cannot be undone.';
        document.getElementById('confirm-modal').classList.remove('hidden');
        document.getElementById('btn-confirm-ok').onclick = async () => {
            document.getElementById('confirm-modal').classList.add('hidden');
            if (type === 'product') {
                await apiFetch('/api/products/' + id, { method: 'DELETE' });
                await loadProducts();
                updateStats();
                showToast('Product deleted successfully');
            } else {
                await apiFetch('/api/categories/' + id, { method: 'DELETE' });
                await loadCategories();
                updateStats();
                showToast('Category deleted successfully');
            }
        };
    }

    function openModal(type, data = null) {
        modalType = type;
        editingId = data ? data.id : null;
        document.getElementById('modal-title').textContent = (data ? 'Edit' : 'Add') + ' ' + (type === 'product' ? 'Product' : 'Category');
        const body = document.getElementById('modal-body');
        if (type === 'product') {
            const catOptions = categories.map(c => `<option value="${c.id}" ${data && data.category_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('');
            body.innerHTML = `
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Product Name <span class="text-red-500">*</span></label><input id="f-name" type="text" value="${data ? data.name : ''}" placeholder="Enter product name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label><select id="f-category" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white">${catOptions}</select></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp) <span class="text-red-500">*</span></label><input id="f-price" type="number" value="${data ? data.price : ''}" placeholder="0" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Stock <span class="text-red-500">*</span></label><input id="f-stock" type="number" value="${data ? data.stock : ''}" placeholder="0" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label><textarea id="f-desc" rows="2" placeholder="Optional description..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm resize-none">${data ? data.description ?? '' : ''}</textarea></div>
                <div class="flex items-center gap-3"><input id="f-active" type="checkbox" ${!data || data.is_active ? 'checked' : ''} class="w-4 h-4 accent-purple-700"><label for="f-active" class="text-sm font-medium text-gray-700">Mark as active</label></div>`;
        } else {
            body.innerHTML = `
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Category Name <span class="text-red-500">*</span></label><input id="f-name" type="text" value="${data ? data.name : ''}" placeholder="Enter category name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label><textarea id="f-desc" rows="2" placeholder="Optional description..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm resize-none">${data ? data.description ?? '' : ''}</textarea></div>`;
        }
        document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        editingId = null;
        modalType = null;
    }

    async function saveModal() {
        const btn = document.getElementById('modal-submit');
        const label = editingId ? 'updated' : 'added';
        btn.innerHTML = '<div class="spinner"></div> Saving...';
        btn.disabled = true;
        if (modalType === 'product') {
            const payload = { name: document.getElementById('f-name').value, category_id: document.getElementById('f-category').value, price: document.getElementById('f-price').value, stock: document.getElementById('f-stock').value, description: document.getElementById('f-desc').value, is_active: document.getElementById('f-active').checked };
            if (editingId) await apiFetch('/api/products/' + editingId, { method: 'PUT', body: JSON.stringify(payload) });
            else await apiFetch('/api/products', { method: 'POST', body: JSON.stringify(payload) });
            closeModal();
            await loadProducts();
            updateStats();
            showToast('Product ' + label + ' successfully');
        } else {
            const payload = { name: document.getElementById('f-name').value, description: document.getElementById('f-desc').value };
            if (editingId) await apiFetch('/api/categories/' + editingId, { method: 'PUT', body: JSON.stringify(payload) });
            else await apiFetch('/api/categories', { method: 'POST', body: JSON.stringify(payload) });
            closeModal();
            await loadCategories();
            updateStats();
            showToast('Category ' + label + ' successfully');
        }
        btn.innerHTML = 'Save';
        btn.disabled = false;
    }

    async function editProduct(id) {
        const data = await apiFetch('/api/products/' + id);
        openModal('product', data);
    }

    async function editCategory(id) {
        const data = await apiFetch('/api/categories/' + id);
        openModal('category', data);
    }

    document.getElementById('btn-login').addEventListener('click', login);
    document.getElementById('btn-logout').addEventListener('click', logout);
    document.getElementById('btn-add-product').addEventListener('click', () => openModal('product'));
    document.getElementById('btn-add-category').addEventListener('click', () => openModal('category'));
    document.getElementById('btn-close-modal').addEventListener('click', closeModal);
    document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
    document.getElementById('modal-submit').addEventListener('click', saveModal);
    document.getElementById('btn-confirm-cancel').addEventListener('click', () => document.getElementById('confirm-modal').classList.add('hidden'));
    document.getElementById('btn-copy-token').addEventListener('click', () => { navigator.clipboard.writeText(token); showToast('Token copied to clipboard'); });

    document.getElementById('search-products').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const filtered = allProducts.filter(p => p.name.toLowerCase().includes(q) || (p.category && p.category.name.toLowerCase().includes(q)));
        const { col, dir } = sortState.products;
        renderProducts(col ? sortData(filtered, col, dir, 'products') : filtered);
    });

    document.getElementById('search-categories').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const filtered = allCategories.filter(c => c.name.toLowerCase().includes(q));
        const { col, dir } = sortState.categories;
        renderCategories(col ? sortData(filtered, col, dir, 'categories') : filtered);
    });

    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            const tableType = th.dataset.table;
            const state = sortState[tableType];
            if (state.col === col) {
                state.dir = state.dir === 'asc' ? 'desc' : 'asc';
            } else {
                state.col = col;
                state.dir = 'asc';
            }
            updateSortHeaders(tableType);
            const q = tableType === 'products'
                ? document.getElementById('search-products').value.toLowerCase()
                : document.getElementById('search-categories').value.toLowerCase();
            if (tableType === 'products') {
                const filtered = q ? allProducts.filter(p => p.name.toLowerCase().includes(q) || (p.category && p.category.name.toLowerCase().includes(q))) : allProducts;
                renderProducts(sortData(filtered, col, state.dir, 'products'));
            } else {
                const filtered = q ? allCategories.filter(c => c.name.toLowerCase().includes(q)) : allCategories;
                renderCategories(sortData(filtered, col, state.dir, 'categories'));
            }
        });
    });

    document.getElementById('login-password').addEventListener('keydown', e => { if (e.key === 'Enter') login(); });

    if (token) {
        apiFetch('/api/me').then(data => {
            if (data.id) { currentUser = data; showDashboard(); }
            else { localStorage.removeItem('token'); token = null; }
        });
    }
</script>
</body>
</html>