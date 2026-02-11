<?php
 require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yoksh Study Planner</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script> 
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- <script src="../assets/scripts/export-service.js"></script> -->
   
    <!-- Tippy.js for Tooltips -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>



    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- External CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-slate-50">

    <div class="flex h-screen w-full overflow-hidden">

        <!-- Sidebar Container (Fixed Width, Full Height) -->
        <aside id="sidebar-container"
            class="w-20 bg-white border-r border-slate-200 h-screen flex flex-col items-center py-6"></aside>

        <!-- Main Content Area (Independent Scrolling) -->
        <main id="main-content" class="flex-1 h-screen overflow-y-auto hide-scrollbar p-8">
            <!-- Content from overview.html or dashboard.html loads here -->
        </main>

    </div>

    <script>
        $(document).ready(function () {
            // Load Sidebar and then initialize Tippy
            $("#sidebar-container").load("includes/sidebar.php", function () {
                initializeTooltips();
            });

            // Initial Content Load
            $("#main-content").load("components/curriculum.php");

            // Navigation Logic
            $(document).on('click', '.nav-icon-link', function (e) {
                e.preventDefault();
                $('.nav-icon-link').removeClass('active');
                $(this).addClass('active');

                let page = $(this).attr('data-page');
                $("#main-content").load("components/" + page + ".php");
            });

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
</body>

</html>