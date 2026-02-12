<?php
include '../../config/db.php';
// 1. Fetch available modules
$modules_query = mysqli_query($conn, "SELECT DISTINCT m.* FROM modules m 
    JOIN chapters c ON m.id = c.module_id 
    JOIN sub_chapters sc ON c.id = sc.chapter_id 
    WHERE sc.is_completed = 0 ORDER BY m.id ASC");

// 2. Fetch Groups (Available ones)
$groups_query = mysqli_query($conn, "
    SELECT g.*, 
    (SELECT COUNT(*) FROM assignments WHERE group_id = g.id AND status = 'active') as is_busy,
    (SELECT COUNT(*) FROM assignments WHERE group_id = g.id) as total_tasks
    FROM study_groups g ORDER BY is_busy ASC, total_tasks ASC");

// 3. Fetch History
$history_query = mysqli_query($conn, "SELECT a.*, g.group_name FROM assignments a JOIN study_groups g ON a.group_id = g.id WHERE a.status = 'active' ORDER BY a.id DESC");
?>

<div class="max-w-full mx-auto animate-fadeIn pb-20">
    <div class="mb-10 bg-white p-6 rounded-md border border-slate-100 shadow-sm">
        <h1 class="text-3xl font-bold text-slate-800">Group Assignments</h1>
        <p class="text-slate-500 mt-1">Deploy unassigned topics to groups.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Curriculum -->
        <div class="lg:col-span-7 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">1. Select Topics</h3>
            <div id="curriculumAccordion" class="space-y-3">

                <?php
                // 1. Check if there are any modules with available (uncompleted) sub-chapters
                if (mysqli_num_rows($modules_query) > 0):
                    while ($mod = mysqli_fetch_assoc($modules_query)): $mid = $mod['id'];
                ?>
                        <div class="module-node bg-white border border-slate-200 rounded-md overflow-hidden shadow-sm" data-module-code="<?php echo $mod['module_code']; ?>">
                            <div class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 toggle-node">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-folder text-indigo-500"></i>
                                    <span class="font-bold text-slate-700">Module: <?php echo htmlspecialchars($mod['module_title']); ?></span>
                                </div>
                                <i class="fas fa-chevron-down text-slate-300 text-xs transition-transform"></i>
                            </div>

                            <div class="node-content hidden border-t border-slate-50 bg-slate-50/30 p-4 space-y-2">
                                <?php
                                $ch_query = mysqli_query($conn, "SELECT * FROM chapters WHERE module_id = '$mid'");
                                while ($chap = mysqli_fetch_assoc($ch_query)): $cid = $chap['id'];
                                    // Only fetch sub-chapters that are NOT completed (status 0)
                                    $sub_query = mysqli_query($conn, "SELECT * FROM sub_chapters WHERE chapter_id = '$cid' AND is_completed = 0");
                                    if (mysqli_num_rows($sub_query) > 0):
                                ?>
                                        <div class="chapter-node bg-white border border-slate-100 rounded-md overflow-hidden">
                                            <div class="p-3 flex items-center justify-between cursor-pointer hover:bg-slate-50 toggle-node">
                                                <div class="flex items-center gap-3">
                                                    <i class="fas fa-book text-emerald-500 text-xs"></i>
                                                    <span class="text-sm font-semibold text-slate-600 chapter-title-val"><?php echo htmlspecialchars($chap['chapter_title']); ?></span>
                                                </div>
                                                <i class="fas fa-plus text-slate-300 text-[10px]"></i>
                                            </div>
                                            <div class="node-content hidden p-4 border-t border-slate-50 bg-white">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <?php while ($sub = mysqli_fetch_assoc($sub_query)): ?>
                                                        <label class="flex items-center gap-3 p-3 border border-slate-100 rounded-md hover:border-indigo-200 cursor-pointer transition">
                                                            <input type="checkbox" class="sub-check w-4 h-4 rounded text-indigo-600"
                                                                data-id="<?php echo $sub['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($sub['sub_title']); ?>">
                                                            <span class="text-xs text-slate-600"><?php echo htmlspecialchars($sub['sub_title']); ?></span>
                                                        </label>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    endif;
                                endwhile;
                                ?>
                            </div>
                        </div>
                    <?php
                    endwhile;
                else:
                    ?>
                    <!-- Refined Empty State for Topics Selection -->
                    <div id="topicsEmptyState" class="py-16 bg-white border-2 border-dashed border-slate-200 rounded-md text-center animate-fadeIn">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-list-check text-2xl"></i>
                        </div>
                        <h3 class="text-slate-600 font-bold text-lg">No Topics Available</h3>
                        <p class="text-slate-400 text-sm mt-2 max-w-xs mx-auto px-4 leading-relaxed">
                            It looks like all topics are currently assigned or completed. Check the <b>Deployment History</b> or <b>Learning Progress</b>.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Group Selection -->
        <div class="lg:col-span-5 space-y-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">2. Assign to Group</h3>
            <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-6 relative">
                <div class="relative z-[100]">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Target Group</label>
                    <div id="groupTrigger" class="w-full bg-slate-50 border border-slate-200 rounded-md px-4 py-3 flex items-center justify-between cursor-pointer hover:border-indigo-400 transition">
                        <span id="selectedGroupLabel" class="text-sm font-semibold text-slate-400">Choose a group...</span>
                        <i class="fas fa-users text-slate-300"></i>
                    </div>
                    <div id="groupListMenu" class="absolute left-0 right-0 mt-2 bg-white border border-slate-200 rounded-md shadow-2xl hidden overflow-hidden">
                        <div class="max-h-60 overflow-y-auto">
                            <?php
                            $available_count = 0;

                            // Ensure the internal pointer is at the start if the query was used elsewhere
                            if (mysqli_num_rows($groups_query) > 0) mysqli_data_seek($groups_query, 0);

                            while ($g = mysqli_fetch_assoc($groups_query)):
                                // Skip groups that are already busy
                                if ($g['is_busy'] > 0) continue;

                                $available_count++;
                                $gid = $g['id'];
                                $m_res = mysqli_query($conn, "SELECT user_name, user_phone FROM users u JOIN group_members gm ON u.id = gm.user_id WHERE gm.group_id = '$gid'");
                                $members = [];
                                while ($m = mysqli_fetch_assoc($m_res)) $members[] = ['name' => $m['user_name'], 'phone' => $m['user_phone']];
                            ?>
                                <!-- Group Option -->
                                <div class="group-select-opt p-4 border-b border-slate-50 hover:bg-indigo-50 cursor-pointer flex justify-between items-center transition"
                                    data-id="<?php echo $g['id']; ?>"
                                    data-name="<?php echo $g['group_name']; ?>"
                                    data-members='<?php echo json_encode($members); ?>'>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($g['group_name']); ?></p>
                                        <p class="text-[10px] text-emerald-500 font-bold uppercase">Status: Available</p>
                                    </div>
                                    <i class="fas fa-check-circle text-emerald-400"></i>
                                </div>
                            <?php endwhile; ?>

                            <?php if ($available_count === 0): ?>
                                <!-- Empty State for Dropdown -->
                                <div class="p-8 text-center animate-fadeIn">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-users-slash text-xl"></i>
                                    </div>
                                    <h4 class="text-slate-600 font-bold text-xs uppercase tracking-tight">No Groups Available</h4>
                                    <p class="text-slate-400 text-[10px] mt-1 leading-relaxed">
                                        All groups are currently occupied or no groups have been created yet.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <button id="deployFinalBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-md font-bold transition-all shadow-lg flex items-center justify-center gap-4">
                    <i class="fas fa-paper-plane text-sm"></i> Deploy Assignment
                </button>
            </div>
        </div>
    </div>

    <!-- Deployment History -->
    <div class="mt-16">
        <h3 class="text-xl font-bold text-slate-800 mb-8 border-b pb-4">Deployment History</h3>
        <div id="activeListContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (mysqli_num_rows($history_query) > 0): ?>
                <?php while ($h = mysqli_fetch_assoc($history_query)):
                    $gid = $h['group_id'];
                    $m_res = mysqli_query($conn, "SELECT user_name, user_phone FROM users u JOIN group_members gm ON u.id = gm.user_id WHERE gm.group_id = '$gid'");
                    $card_members = [];
                    while ($m = mysqli_fetch_assoc($m_res)) $card_members[] = ['name' => $m['user_name'], 'phone' => $m['user_phone']];
                ?>
                    <div class="assignment-card bg-white border border-slate-200 rounded-md p-6 shadow-sm flex flex-col h-full" data-id="<?php echo $h['id']; ?>">
                        <div class="flex justify-between items-start mb-6">
                            <div class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded uppercase"><?php echo $h['group_name']; ?></div>
                            <button class="remove-assign text-slate-300 hover:text-rose-500"><i class="fas fa-trash-alt text-xs"></i></button>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1"><?php echo $h['chapter_name']; ?></h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Module Code: <?php echo $h['module_code']; ?></p>

                        <div class="space-y-1.5 flex-1 border-t border-slate-50 pt-4">
                            <?php
                            // Show topics one below another
                            $topics = explode("\n", $h['topic_names']);
                            foreach ($topics as $t): if (trim($t) != ""): ?>
                                    <div class="flex items-start gap-2 text-xs text-slate-600">
                                        <i class="fas fa-circle text-[4px] mt-1.5 text-indigo-400"></i>
                                        <span><?php echo $t; ?></span>
                                    </div>
                            <?php endif;
                            endforeach; ?>
                        </div>

                        <div class="mt-8">
                            <button onclick='window.exportAssignmentImage("<?php echo $h['group_name']; ?>", `<?php echo $h['topic_names']; ?>`, "<?php echo $h['module_code']; ?>", <?php echo json_encode($card_members); ?>, "<?php echo addslashes($h['chapter_name']); ?>")'
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-md text-xs font-bold transition flex items-center justify-center gap-2">
                                <i class="fas fa-file-download"></i> Save as Image
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Refined Empty State for Deployment History -->
                <div id="assignmentEmptyMsg" class="col-span-full py-20 bg-white border-2 border-dashed border-slate-200 rounded-md text-center animate-fadeIn">
                    <!-- Icon Circle -->
                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-paper-plane text-2xl"></i>
                    </div>

                    <!-- Title -->
                    <h3 class="text-slate-600 font-bold text-lg">No Active Assignments</h3>

                    <!-- Description -->
                    <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto px-8 leading-relaxed">
                        You haven't deployed any study passes yet. Select your topics and an available group above, then click
                        <span class="text-indigo-600 font-bold">"Deploy Assignment"</span> to get started.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var selGroupId = "",
            selGroupName = "",
            selMembers = [];

        $(document).on('click', '.toggle-node', function() {
            $(this).next('.node-content').slideToggle(200);
            $(this).find('i.fa-chevron-down, i.fa-plus').toggleClass('rotate-180 rotate-45 text-indigo-500');
        });

        $('#groupTrigger').on('click', function(e) {
            e.stopPropagation();
            $('#groupListMenu').toggle();
        });

        $(document).on('click', '.group-select-opt', function() {
            selGroupId = $(this).data('id');
            selGroupName = $(this).data('name');
            selMembers = $(this).data('members');
            $('#selectedGroupLabel').text(selGroupName).removeClass('text-slate-400').addClass('text-slate-800');
            $('#groupListMenu').hide();
        });

        $(document).on('click', function() {
            $('#groupListMenu').hide();
        });

        $('#deployFinalBtn').on('click', function() {
            var topicsArr = [];
            var topicIds = [];
            var firstChapter = "";
            var moduleCode = "";

            $('.sub-check:checked').each(function() {
                if (!firstChapter) firstChapter = $(this).closest('.chapter-node').find('.chapter-title-val').text();
                if (!moduleCode) moduleCode = $(this).closest('.module-node').attr('data-module-code');
                topicsArr.push($(this).attr('data-name'));
                topicIds.push($(this).attr('data-id'));
            });

            if (!selGroupId || topicIds.length === 0) {
                showToast("Missing Info", "warning", "Select a group and topics.");
                return;
            }

            $.ajax({
                url: 'handlers/assignments-action.php',
                type: 'POST',
                data: {
                    action: 'save',
                    group_id: selGroupId,
                    chapter_name: firstChapter,
                    module_code: "EPU-" + moduleCode,
                    topics_text: topicsArr.join("\n"), // Formats topics one below another
                    topic_ids: topicIds
                },
                success: function(res) {
                    if (res.status === 'success') {
                        showToast(res.title, 'success', res.description);
                        setTimeout(() => location.reload(), 800);
                    }
                }
            });
        });

        $(document).on('click', '.remove-assign', function() {
            var aid = $(this).closest('.assignment-card').data('id');
            showConfirmation("Remove Assignment?", "Topics will be unlocked.", function() {
                $.ajax({
                    url: 'handlers/assignments-action.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        assign_id: aid
                    },
                    success: function(res) {
                        location.reload();
                    }
                });
            });
        });
    });
</script>