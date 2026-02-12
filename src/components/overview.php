<?php
include '../../config/db.php';

// 1. Fetch Quick Stats
$total_modules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM modules"))['count'];
$total_chapters = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM chapters"))['count'];
$total_subs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sub_chapters"))['count'];
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];

// 2. Weekly Growth (Last 7 days)
$new_modules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM modules WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];
$new_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];

// 3. Overall Completion Percentage
$completion_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as done 
    FROM sub_chapters"));
$total_perc = ($completion_res['total'] > 0) ? round(($completion_res['done'] / $completion_res['total']) * 100, 1) : 0;

// 4. Fetch Recent Activity (Last 5 actions)
$activities = mysqli_query($conn, "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 5");

// 5. Chart Data (Completions per day for last 7 days)
$chart_labels = [];
$chart_values = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    $val_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM activity_log WHERE DATE(created_at) = '$date' AND activity_description LIKE 'Completed%'"));
    $chart_labels[] = $day_name;
    $chart_values[] = $val_res['count'];
}
?>

<div id="overview-content" class="space-y-8 animate-fadeIn">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-md border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Overview</h1>
            <p class="text-slate-500 text-sm">Monitor your study milestones and activities.</p>
        </div>
        <div class="flex items-center gap-3">
            <?php
            // Set timezone specifically for this output if not set globally
            date_default_timezone_set('Asia/Colombo');
            ?>
            <span class="text-sm font-medium text-slate-500">
                Last synced: <?php echo date('h:i A'); ?>
            </span>
            <button onclick="location.reload()" class="p-2 bg-white border border-slate-200 rounded-md hover:bg-slate-50 transition">
                <i class="fas fa-sync-alt text-slate-400 text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="bg-white p-5 rounded-md border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-sky-100 text-sky-600 rounded-md flex items-center justify-center"><i class="fas fa-cubes"></i></div>
                <span class="text-xs font-bold text-sky-600 bg-sky-50 px-2 py-1 rounded-md">+<?php echo $new_modules; ?> this week</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Modules</p>
            <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_modules; ?></h3>
        </div>

        <div class="bg-white p-5 rounded-md border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-md flex items-center justify-center"><i class="fas fa-book-open"></i></div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Real-time</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Chapters</p>
            <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_chapters; ?></h3>
        </div>

        <div class="bg-white p-5 rounded-md border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-md flex items-center justify-center"><i class="fas fa-list-ul"></i></div>
                <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2 py-1 rounded-md"><?php echo $total_subs; ?> Total</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Sub-Chapters</p>
            <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_subs; ?></h3>
        </div>

        <div class="bg-white p-5 rounded-md border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-md flex items-center justify-center"><i class="fas fa-user"></i></div>
                <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-md">+<?php echo $new_students; ?> this week</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Students</p>
            <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_students; ?></h3>
        </div>

        <div class="bg-white p-5 rounded-md border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-md flex items-center justify-center"><i class="fas fa-chart-line"></i></div>
                <div class="flex items-center text-rose-600 text-xs font-bold">Progress</div>
            </div>
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Completion</p>
            <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_perc; ?>%</h3>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800">Learning Activity</h3>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Topic Completions</p>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <!-- Module Progress Breakdown -->
            <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-6">Module Breakdown</h3>
                <div class="space-y-6">
                    <?php
                    $breakdown_query = mysqli_query($conn, "SELECT m.module_title, 
                    (SELECT COUNT(*) FROM chapters c JOIN sub_chapters s ON c.id = s.chapter_id WHERE c.module_id = m.id) as total,
                    (SELECT COUNT(*) FROM chapters c JOIN sub_chapters s ON c.id = s.chapter_id WHERE c.module_id = m.id AND s.is_completed = 1) as done
                    FROM modules m LIMIT 4");

                    // 1. Check if there are any modules to display
                    if (mysqli_num_rows($breakdown_query) > 0):
                        while ($row = mysqli_fetch_assoc($breakdown_query)):
                            $m_perc = ($row['total'] > 0) ? round(($row['done'] / $row['total']) * 100) : 0;
                    ?>
                            <!-- Module Progress Item -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($row['module_title']); ?></span>
                                    <span class="text-sm font-bold text-sky-600"><?php echo $m_perc; ?>%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-md overflow-hidden">
                                    <div class="bg-sky-400 h-full rounded-md transition-all duration-1000" style="width: <?php echo $m_perc; ?>%"></div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <!-- 2. Empty State Message -->
                        <div class="py-10 text-center animate-fadeIn">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-pie text-2xl"></i>
                            </div>
                            <h4 class="text-slate-600 font-bold text-sm">No Progress Data</h4>
                            <p class="text-slate-400 text-xs mt-1 px-6 leading-relaxed">
                                Create your first module and chapters to see your detailed progress breakdown here.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Grid -->
        <div class="space-y-8">
            <!-- Calendar UI -->
            <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800">Calendar</h3>
                </div>
                <div id="calendar-month-year" class="text-center text-sm font-bold text-slate-600 mb-4 uppercase tracking-tighter"></div>
                <div class="grid grid-cols-7 gap-1 text-center mb-2">
                    <?php foreach (['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'] as $day) echo "<div class='text-[10px] font-bold text-slate-400 uppercase'>$day</div>"; ?>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center" id="calendar-days"></div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-6">Recent Activity</h3>

                <?php if (mysqli_num_rows($activities) > 0): ?>
                    <!-- Timeline Container (Only shows if data exists) -->
                    <div class="space-y-6 relative before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        <?php
                        // Reset result pointer to start (in case $activities was checked before)
                        mysqli_data_seek($activities, 0);
                        while ($act = mysqli_fetch_assoc($activities)):
                            $icon = "fa-check";
                            $color = "bg-emerald-100 text-emerald-600";

                            if (strpos($act['activity_description'], 'Added') !== false) {
                                $icon = "fa-plus";
                                $color = "bg-sky-100 text-sky-600";
                            }
                            if (strpos($act['activity_description'], 'Deleted') !== false) {
                                $icon = "fa-trash";
                                $color = "bg-rose-100 text-rose-600";
                            }
                        ?>
                            <div class="relative pl-8">
                                <!-- Icon Circle -->
                                <div class="absolute left-0 top-1 w-7 h-7 <?php echo $color; ?> rounded-md flex items-center justify-center text-[10px] z-10">
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <!-- Description & Time -->
                                <p class="text-sm font-semibold text-slate-700 line-clamp-1"><?php echo htmlspecialchars($act['activity_description']); ?></p>
                                <p class="text-[10px] text-slate-400"><?php echo date('M d, h:i A', strtotime($act['created_at'])); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <!-- Empty State Message -->
                    <div class="py-12 text-center animate-fadeIn">
                        <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock-rotate-left text-2xl"></i>
                        </div>
                        <h4 class="text-slate-600 font-bold text-sm">All Quiet Here</h4>
                        <p class="text-slate-400 text-xs mt-1 px-6 leading-relaxed">
                            Your recent actions will be logged here automatically as you manage your study planner.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // --- 1. CHART LOGIC ---
        const ctx = document.getElementById('activityChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');


        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: '#4338ca',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4338ca'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f8fafc'
                        },
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

        // --- 2. DYNAMIC CALENDAR LOGIC ---
        function generateCalendar() {
            const now = new Date();
            const month = now.getMonth();
            const year = now.getFullYear();
            const today = now.getDate();

            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $('#calendar-month-year').text(`${monthNames[month]} ${year}`);

            const firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            let html = '';
            // Leading days from previous month (adjusting for Monday start)
            let startOffset = firstDay === 0 ? 6 : firstDay - 1;
            for (let i = startOffset; i > 0; i--) {
                html += `<div class="py-2 text-xs text-slate-200">${daysInPrevMonth - i + 1}</div>`;
            }

            // Current month days
            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = day === today ? 'bg-indigo-500 text-white font-bold shadow-md shadow-indigo-100' : 'text-slate-800 hover:bg-sky-50';
                html += `<div class="py-2 text-xs rounded-md cursor-pointer transition ${isToday}">${day}</div>`;
            }
            $('#calendar-days').html(html);
        }

        generateCalendar();
    });
</script>