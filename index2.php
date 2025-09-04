<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccounTech Pro - Spreadsheet Accounting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .toast { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        .modal-backdrop { backdrop-filter: blur(4px); }
        .table-hover:hover { background-color: #f8fafc; }
        .balance-error { animation: shake 0.5s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Login Page -->
    <div id="loginPage" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-purple-700">
        <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="text-center mb-8">
                <i class="fas fa-calculator text-4xl text-blue-600 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800">AccounTech Pro</h1>
                <p class="text-gray-600 mt-2">Spreadsheet-Backed Accounting</p>
            </div>
            
            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="loginEmail" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="admin@company.com" value="admin@company.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="loginPassword" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••••" value="admin123">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Forgot password?</a>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Demo Accounts:</p>
                <p>Admin: admin@company.com / admin123</p>
                <p>Accountant: accountant@company.com / acc123</p>
                <p>User: user@company.com / user123</p>
            </div>
        </div>
    </div>

    <!-- Main App -->
    <div id="mainApp" class="hidden min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-40">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="flex items-center ml-4 lg:ml-0">
                            <i class="fas fa-calculator text-2xl text-blue-600 mr-3"></i>
                            <h1 class="text-xl font-bold text-gray-800">AccounTech Pro</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <span id="currentDateTime"></span>
                        </div>
                        <div class="relative">
                            <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    <span id="userInitials">A</span>
                                </div>
                                <span id="userName" class="hidden sm:block">Admin User</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="#" onclick="showPage('profile')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" onclick="logout()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 sidebar-transition">
            <div class="flex flex-col h-full pt-16">
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="#" onclick="showPage('dashboard')" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="#" onclick="showPage('gl')" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-book mr-3"></i>General Ledger
                    </a>
                    <a href="#" onclick="showPage('coa')" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-list mr-3"></i>Chart of Accounts
                    </a>
                    <a href="#" onclick="showPage('reports')" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-chart-bar mr-3"></i>Reports
                    </a>
                    <a href="#" onclick="showPage('users')" id="usersNav" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-users mr-3"></i>User Management
                    </a>
                    <a href="#" onclick="showPage('controls')" id="controlsNav" class="nav-item flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition duration-200">
                        <i class="fas fa-shield-alt mr-3"></i>Financial Controls
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:ml-64 pt-16">
            <main class="p-6">
                <!-- Dashboard Page -->
                <div id="dashboardPage" class="page">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Dashboard</h2>
                        <p class="text-gray-600 mt-2">Welcome back! Here's your accounting overview.</p>
                    </div>

                    <!-- KPI Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Today's Entries</p>
                                    <p class="text-3xl font-bold text-gray-900">12</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">MTD Debits</p>
                                    <p class="text-3xl font-bold text-green-600">$45,230</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-arrow-up text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">MTD Credits</p>
                                    <p class="text-3xl font-bold text-red-600">$45,230</p>
                                </div>
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-arrow-down text-red-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                                    <p class="text-3xl font-bold text-yellow-600">3</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widgets Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Recent Entries -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Entries</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">JE-2025-001</p>
                                            <p class="text-sm text-gray-600">Office Supplies Purchase</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">$1,250.00</p>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Posted</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">JE-2025-002</p>
                                            <p class="text-sm text-gray-600">Client Payment Received</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">$5,000.00</p>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Draft</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button onclick="showPage('gl')" class="w-full text-center text-blue-600 hover:text-blue-800 font-medium">
                                        View All Entries →
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Top Accounts -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Top Accounts This Month</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">1000 - Cash</p>
                                            <p class="text-sm text-gray-600">Asset</p>
                                        </div>
                                        <p class="font-medium text-gray-900">$15,230</p>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">4000 - Revenue</p>
                                            <p class="text-sm text-gray-600">Revenue</p>
                                        </div>
                                        <p class="font-medium text-gray-900">$12,500</p>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">5000 - Expenses</p>
                                            <p class="text-sm text-gray-600">Expense</p>
                                        </div>
                                        <p class="font-medium text-gray-900">$8,750</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button onclick="showPage('gl')" class="p-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Add Journal Entry
                            </button>
                            <button onclick="showPage('reports')" class="p-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                <i class="fas fa-chart-line mr-2"></i>View Reports
                            </button>
                            <button onclick="showPage('coa')" class="p-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                                <i class="fas fa-cog mr-2"></i>Manage Accounts
                            </button>
                        </div>
                    </div>
                </div>

                <!-- General Ledger Page -->
                <div id="glPage" class="page hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">General Ledger</h2>
                        <p class="text-gray-600 mt-2">Create and manage journal entries with double-entry validation.</p>
                    </div>

                    <!-- Entry Form -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">New Journal Entry</h3>
                        </div>
                        <div class="p-6">
                            <form id="journalEntryForm">
                                <!-- Header Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                        <input type="date" id="entryDate" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference No.</label>
                                        <input type="text" id="refNo" readonly 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                                               value="JE-2025-003">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select id="entryStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="draft">Draft</option>
                                            <option value="posted">Posted</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description/Memo</label>
                                    <textarea id="entryMemo" rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Enter journal entry description..."></textarea>
                                </div>

                                <!-- Journal Lines -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-medium text-gray-900">Journal Lines</h4>
                                        <button type="button" onclick="addJournalLine()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                            <i class="fas fa-plus mr-2"></i>Add Line
                                        </button>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full border border-gray-300 rounded-lg">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Account</th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Debit</th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Credit</th>
                                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="journalLines">
                                                <!-- Lines will be added dynamically -->
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr>
                                                    <td colspan="2" class="px-4 py-3 text-right font-medium text-gray-900">Totals:</td>
                                                    <td class="px-4 py-3 font-bold text-gray-900" id="totalDebits">$0.00</td>
                                                    <td class="px-4 py-3 font-bold text-gray-900" id="totalCredits">$0.00</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span id="balanceStatus" class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Out of Balance</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex flex-wrap gap-4">
                                    <button type="button" onclick="saveJournalEntry('draft')" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                                        Save Draft
                                    </button>
                                    <button type="button" onclick="saveJournalEntry('posted')" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                        Post Entry
                                    </button>
                                    <button type="button" onclick="clearJournalForm()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                        Clear Form
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Entries Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Journal Entries</h3>
                                <div class="flex space-x-2">
                                    <input type="text" placeholder="Search entries..." 
                                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Reference</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Amount</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Prepared By</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" id="entriesTable">
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm text-gray-900">2025-01-15</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">JE-2025-001</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Office Supplies Purchase</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">$1,250.00</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Posted</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Admin User</td>
                                        <td class="px-6 py-4 text-center">
                                            <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Chart of Accounts Page -->
                <div id="coaPage" class="page hidden">
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">Chart of Accounts</h2>
                                <p class="text-gray-600 mt-2">Manage your accounting structure and account hierarchy.</p>
                            </div>
                            <button onclick="openAccountModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Add Account
                            </button>
                        </div>
                    </div>

                    <!-- Accounts Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Accounts</h3>
                                <div class="flex space-x-2">
                                    <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">All Types</option>
                                        <option value="asset">Assets</option>
                                        <option value="liability">Liabilities</option>
                                        <option value="equity">Equity</option>
                                        <option value="revenue">Revenue</option>
                                        <option value="expense">Expenses</option>
                                    </select>
                                    <input type="text" placeholder="Search accounts..." 
                                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Account No.</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Account Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Type</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Parent Account</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" id="accountsTable">
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">1000</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Cash</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Asset</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">-</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button onclick="editAccount('1000')" class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deactivateAccount('1000')" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">2000</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Accounts Payable</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Liability</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">-</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button onclick="editAccount('2000')" class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deactivateAccount('2000')" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reports Page -->
                <div id="reportsPage" class="page hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Reports & Analysis</h2>
                        <p class="text-gray-600 mt-2">Generate filtered reports and analyze your financial data.</p>
                    </div>

                    <!-- Filter Panel -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Report Filters</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                    <div class="space-y-2">
                                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">All Types</option>
                                        <option value="asset">Assets</option>
                                        <option value="liability">Liabilities</option>
                                        <option value="equity">Equity</option>
                                        <option value="revenue">Revenue</option>
                                        <option value="expense">Expenses</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">All Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="posted">Posted</option>
                                        <option value="approved">Approved</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Range</label>
                                    <div class="space-y-2">
                                        <input type="number" placeholder="Min" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <input type="number" placeholder="Max" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex space-x-4">
                                <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-search mr-2"></i>Generate Report
                                </button>
                                <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-download mr-2"></i>Export CSV
                                </button>
                                <button class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Report Results -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Report Results</h3>
                                <div class="text-sm text-gray-600">
                                    Total Entries: 25 | Total Debits: $45,230 | Total Credits: $45,230
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Reference</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Account</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Debit</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Credit</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm text-gray-900">2025-01-15</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">JE-2025-001</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">1000 - Cash</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Office Supplies Purchase</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">$1,250.00</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">-</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Posted</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Management Page -->
                <div id="usersPage" class="page hidden">
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">User Management</h2>
                                <p class="text-gray-600 mt-2">Manage user accounts, roles, and permissions.</p>
                            </div>
                            <button onclick="openUserModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                <i class="fas fa-user-plus mr-2"></i>Add User
                            </button>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">System Users</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Role</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Last Login</th>
                                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">Admin User</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">admin@company.com</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Admin</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">2025-01-15 09:30</td>
                                        <td class="px-6 py-4 text-center">
                                            <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Financial Controls Page -->
                <div id="controlsPage" class="page hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Financial Controls</h2>
                        <p class="text-gray-600 mt-2">Advanced accounting controls for period locks, approvals, and audit trails.</p>
                    </div>

                    <!-- Controls Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Period Locks -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Period Locks</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">2024-12 (December 2024)</p>
                                            <p class="text-sm text-gray-600">Year-end closing period</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Locked</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">2025-01 (January 2025)</p>
                                            <p class="text-sm text-gray-600">Current period</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Open</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition duration-200">
                                        <i class="fas fa-lock mr-2"></i>Lock Current Period
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Queue -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Approval Queue</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">JE-2025-002</p>
                                            <p class="text-sm text-gray-600">Client Payment - $5,000</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Approve
                                            </button>
                                            <button class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Log -->
                    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Audit Trail</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Timestamp</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">User</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Action</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Reference</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="table-hover">
                                        <td class="px-6 py-4 text-sm text-gray-900">2025-01-15 14:30:25</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Admin User</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Entry Posted</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">JE-2025-001</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Office Supplies Purchase - $1,250</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Account Modal -->
    <div id="accountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Add New Account</h3>
            </div>
            <div class="p-6">
                <form id="accountForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                        <input type="text" id="accountNo" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                        <input type="text" id="accountName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                        <select id="accountType" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="accountNotes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                </form>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-4">
                <button onclick="closeAccountModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="saveAccount()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">Save Account</button>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let currentUser = null;
        let currentPage = 'dashboard';
        let journalLineCounter = 0;

        // Mock data
        const mockAccounts = [
            { no: '1000', name: 'Cash', type: 'asset', active: true },
            { no: '1100', name: 'Accounts Receivable', type: 'asset', active: true },
            { no: '2000', name: 'Accounts Payable', type: 'liability', active: true },
            { no: '3000', name: 'Owner Equity', type: 'equity', active: true },
            { no: '4000', name: 'Revenue', type: 'revenue', active: true },
            { no: '5000', name: 'Office Expenses', type: 'expense', active: true }
        ];

        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Set today's date in entry form
            document.getElementById('entryDate').value = new Date().toISOString().split('T')[0];
            
            // Add initial journal lines
            addJournalLine();
            addJournalLine();
        });

        // Authentication
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // Mock authentication
            let role = 'user';
            let name = 'User';
            
            if (email === 'admin@company.com' && password === 'admin123') {
                role = 'admin';
                name = 'Admin User';
            } else if (email === 'accountant@company.com' && password === 'acc123') {
                role = 'accountant';
                name = 'Accountant User';
            }
            
            currentUser = { email, role, name };
            
            // Update UI based on role
            document.getElementById('userName').textContent = name;
            document.getElementById('userInitials').textContent = name.split(' ').map(n => n[0]).join('');
            
            // Show/hide navigation based on role
            document.getElementById('usersNav').style.display = role === 'admin' ? 'block' : 'none';
            document.getElementById('controlsNav').style.display = role === 'accountant' || role === 'admin' ? 'block' : 'none';
            
            // Switch to main app
            document.getElementById('loginPage').classList.add('hidden');
            document.getElementById('mainApp').classList.remove('hidden');
            
            showToast('Login successful!', 'success');
        });

        function logout() {
            currentUser = null;
            document.getElementById('mainApp').classList.add('hidden');
            document.getElementById('loginPage').classList.remove('hidden');
            showToast('Logged out successfully', 'info');
        }

        // Navigation
        function showPage(page) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(p => p.classList.add('hidden'));
            
            // Show selected page
            document.getElementById(page + 'Page').classList.remove('hidden');
            
            // Update navigation active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'text-blue-700');
            });
            
            currentPage = page;
            
            // Close sidebar on mobile
            if (window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
            }
        }

        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });

        // Profile dropdown
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#profileDropdown')) {
                document.getElementById('profileMenu').classList.add('hidden');
            }
        });

        // Journal Entry Functions
        function addJournalLine() {
            const tbody = document.getElementById('journalLines');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3">
                    <select class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="updateTotals()">
                        <option value="">Select Account</option>
                        ${mockAccounts.map(acc => `<option value="${acc.no}">${acc.no} - ${acc.name}</option>`).join('')}
                    </select>
                </td>
                <td class="px-4 py-3">
                    <input type="text" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Description">
                </td>
                <td class="px-4 py-3">
                    <input type="number" step="0.01" class="debit-input w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00" onchange="updateTotals()" oninput="clearCredit(this)">
                </td>
                <td class="px-4 py-3">
                    <input type="number" step="0.01" class="credit-input w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00" onchange="updateTotals()" oninput="clearDebit(this)">
                </td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeJournalLine(this)" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            updateTotals();
        }

        function removeJournalLine(button) {
            const row = button.closest('tr');
            row.remove();
            updateTotals();
        }

        function clearCredit(debitInput) {
            const row = debitInput.closest('tr');
            const creditInput = row.querySelector('.credit-input');
            if (debitInput.value) {
                creditInput.value = '';
            }
        }

        function clearDebit(creditInput) {
            const row = creditInput.closest('tr');
            const debitInput = row.querySelector('.debit-input');
            if (creditInput.value) {
                debitInput.value = '';
            }
        }

        function updateTotals() {
            const debitInputs = document.querySelectorAll('.debit-input');
            const creditInputs = document.querySelectorAll('.credit-input');
            
            let totalDebits = 0;
            let totalCredits = 0;
            
            debitInputs.forEach(input => {
                totalDebits += parseFloat(input.value) || 0;
            });
            
            creditInputs.forEach(input => {
                totalCredits += parseFloat(input.value) || 0;
            });
            
            document.getElementById('totalDebits').textContent = '$' + totalDebits.toFixed(2);
            document.getElementById('totalCredits').textContent = '$' + totalCredits.toFixed(2);
            
            const balanceStatus = document.getElementById('balanceStatus');
            const isBalanced = totalDebits === totalCredits && totalDebits > 0;
            
            if (isBalanced) {
                balanceStatus.textContent = 'Balanced';
                balanceStatus.className = 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
            } else {
                balanceStatus.textContent = 'Out of Balance';
                balanceStatus.className = 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800';
            }
        }

        function saveJournalEntry(status) {
            const totalDebits = parseFloat(document.getElementById('totalDebits').textContent.replace('$', ''));
            const totalCredits = parseFloat(document.getElementById('totalCredits').textContent.replace('$', ''));
            
            if (totalDebits !== totalCredits || totalDebits === 0) {
                showToast('Entry must be balanced and have amounts greater than zero!', 'error');
                document.getElementById('balanceStatus').parentElement.parentElement.classList.add('balance-error');
                setTimeout(() => {
                    document.getElementById('balanceStatus').parentElement.parentElement.classList.remove('balance-error');
                }, 500);
                return;
            }
            
            // Mock save to spreadsheet
            const entryData = {
                date: document.getElementById('entryDate').value,
                refNo: document.getElementById('refNo').value,
                memo: document.getElementById('entryMemo').value,
                status: status,
                preparedBy: currentUser.name,
                lines: []
            };
            
            // Collect line data
            const rows = document.querySelectorAll('#journalLines tr');
            rows.forEach(row => {
                const account = row.querySelector('select').value;
                const description = row.querySelector('input[type="text"]').value;
                const debit = parseFloat(row.querySelector('.debit-input').value) || 0;
                const credit = parseFloat(row.querySelector('.credit-input').value) || 0;
                
                if (account && (debit > 0 || credit > 0)) {
                    entryData.lines.push({ account, description, debit, credit });
                }
            });
            
            console.log('Saving to spreadsheet:', entryData);
            
            showToast(`Journal entry ${status === 'posted' ? 'posted' : 'saved as draft'} successfully!`, 'success');
            
            if (status === 'posted') {
                clearJournalForm();
            }
        }

        function clearJournalForm() {
            document.getElementById('entryMemo').value = '';
            document.getElementById('journalLines').innerHTML = '';
            addJournalLine();
            addJournalLine();
            updateTotals();
            
            // Generate new reference number
            const refNo = document.getElementById('refNo');
            const currentNum = parseInt(refNo.value.split('-')[2]);
            refNo.value = `JE-2025-${String(currentNum + 1).padStart(3, '0')}`;
        }

        // Account Management
        function openAccountModal() {
            document.getElementById('accountModal').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('accountModal').classList.add('hidden');
            document.getElementById('accountForm').reset();
        }

        function saveAccount() {
            const accountNo = document.getElementById('accountNo').value;
            const accountName = document.getElementById('accountName').value;
            const accountType = document.getElementById('accountType').value;
            
            if (!accountNo || !accountName || !accountType) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            // Mock save to spreadsheet
            console.log('Saving account:', { accountNo, accountName, accountType });
            
            showToast('Account created successfully!', 'success');
            closeAccountModal();
        }

        function editAccount(accountNo) {
            showToast(`Editing account ${accountNo}`, 'info');
        }

        function deactivateAccount(accountNo) {
            if (confirm(`Are you sure you want to deactivate account ${accountNo}?`)) {
                showToast(`Account ${accountNo} deactivated`, 'success');
            }
        }

        // User Management
        function openUserModal() {
            showToast('User management modal would open here', 'info');
        }

        // Utility Functions
        function updateDateTime() {
            const now = new Date();
            const options = {
                timeZone: 'Asia/Kuching',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = now.toLocaleString('en-MY', options);
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            toast.className = `toast ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle"></i>
                <span>${message}</span>
            `;
            
            document.getElementById('toastContainer').appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                closeAccountModal();
            }
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9795b91ee7c434c9',t:'MTc1NjkwNzUwMC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
