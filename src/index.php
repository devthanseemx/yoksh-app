<?php
include '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yoksh Study Planner</title>

    <!-- ======= Stylesheets ======= -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- ======= Core Libraries ======= -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="bg-slate-50">

    <div class="flex h-screen w-full overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside id="sidebar-container" class="w-20 bg-white border-r border-slate-200 h-screen flex flex-col items-center py-6">
            <!-- Loaded via AJAX -->
        </aside>

        <!-- Main Dashboard Content -->
        <main id="main-content" class="flex-1 h-screen overflow-y-auto hide-scrollbar p-8">
            <!-- Dynamic components load here -->
        </main>
    </div>

    <!-- ======= UI Modals ======= -->

    <!-- Action Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center z-[9999]">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-sm w-full text-center animate-fadeIn">
            <div class="flex justify-center mb-4 text-amber-500 text-5xl">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <h3 id="confirmMessage" class="text-xl font-bold text-slate-800 mb-2"></h3>
            <p id="confirmDescription" class="text-sm text-slate-500 mb-8"></p>
            <div class="flex justify-center gap-3">
                <button id="noBtn" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 font-semibold transition">No, Cancel</button>
                <button id="yesBtn" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition shadow-sm shadow-indigo-200">Yes, Confirm</button>
            </div>
        </div>
    </div>

    <!-- System Information Modal -->
    <div id="okConfirmModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm items-center justify-center z-[9999]">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-sm w-full text-center animate-fadeIn">
            <div class="flex justify-center mb-4 text-indigo-500 text-5xl">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <h3 id="confirmMessage" class="text-xl font-bold text-slate-800 mb-2"></h3>
            <p id="confirmDescription" class="text-sm text-slate-500 mb-8"></p>
            <button id="yesBtn" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">Great, thanks!</button>
        </div>
    </div>

    <!-- ======= Application Logic ======= -->
    <script>
        $(document).ready(function() {
            // Load Navigation and Tooltips
            $("#sidebar-container").load("includes/sidebar.php", function() {
                initializeTooltips();
            });

            // Default Page Load
            $("#main-content").load("components/curriculum.php");

            // Sidebar Navigation Handler
            $(document).on('click', '.nav-icon-link', function(e) {
                e.preventDefault();
                $('.nav-icon-link').removeClass('active');
                $(this).addClass('active');

                let page = $(this).attr('data-page');
                $("#main-content").load("components/" + page + ".php");
            });

            // Initialize Tippy tooltips for navigation
            function initializeTooltips() {
                tippy('[data-tippy-content]', {
                    placement: 'right',
                    theme: 'material',
                    animation: 'shift-away',
                    arrow: true
                });
            }
        });
    </script>

    <!-- External UI Helpers (Toasts & Modals Logic) -->
    <script src="../assets/js/ui-helpers.js"></script>
</body>

</html>