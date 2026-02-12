<?php
include '../../config/db.php';

$groups_query = mysqli_query($conn, "
    SELECT g.*, COUNT(m.id) as member_count 
    FROM study_groups g 
    LEFT JOIN group_members m ON g.id = m.group_id 
    GROUP BY g.id ORDER BY g.id ASC
");

// etch users who ARE NOT in any group
$available_users_query = mysqli_query($conn, "
    SELECT * FROM users 
    WHERE id NOT IN (SELECT user_id FROM group_members)
    ORDER BY user_name ASC
");

//Determine next Group Name using MAX(id)
$max_id_res = mysqli_query($conn, "SELECT MAX(id) as max_id FROM study_groups");
$max_id_row = mysqli_fetch_assoc($max_id_res);
$next_num = ($max_id_row['max_id'] ? $max_id_row['max_id'] : 0) + 1;
$next_group_name = "Group-" . $next_num;

// Helper array for random colors
$avatar_colors = [
    'bg-indigo-100 text-indigo-600',
    'bg-emerald-100 text-emerald-600',
    'bg-rose-100 text-rose-600',
    'bg-sky-100 text-sky-600',
    'bg-amber-100 text-amber-600',
    'bg-purple-100 text-purple-600'
];
?>

<div class="max-w-full mx-auto animate-fadeIn pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 bg-white p-6 rounded-md border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Group Management</h1>
            <p class="text-slate-500 mt-1">Organize users into automatically named study groups.</p>
        </div>
        <button id="openGroupModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-md font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-100">
            <i class="fas fa-plus-circle"></i> Create New Group
        </button>
    </div>

    <!-- Groups Grid -->
    <div id="groupsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">

        <?php if (mysqli_num_rows($groups_query) > 0): ?>
            <?php while ($g = mysqli_fetch_assoc($groups_query)):
                $g_id = $g['id'];
                // Fetch member names for the avatars on the card
                $members_res = mysqli_query($conn, "
                    SELECT u.user_name FROM users u 
                    JOIN group_members gm ON u.id = gm.user_id 
                    WHERE gm.group_id = '$g_id'
                ");
            ?>
                <div class="group-card bg-white border border-slate-200 rounded-md p-5 shadow-sm hover:shadow-md transition-all animate-fadeIn" data-id="<?php echo $g_id; ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-indigo-600 text-white rounded-md flex items-center justify-center shadow-md shadow-indigo-100">
                            <i class="fas fa-users-viewfinder text-lg"></i>
                        </div>
                        <button class="delete-group text-slate-300 hover:text-rose-500 transition"><i class="fas fa-trash-alt text-xs"></i></button>
                    </div>

                    <h3 class="font-bold text-slate-800 text-lg mb-1"><?php echo $g['group_name']; ?></h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Created: <?php echo date('M d, Y', strtotime($g['created_at'])); ?></p>

                    <div class="space-y-2 border-t border-slate-50 pt-4">
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-slate-500">Members:</span>
                            <span class="text-indigo-600 font-bold"><?php echo $g['member_count']; ?> People</span>
                        </div>

                        <!-- Member Avatars Row -->
                        <div class="flex -space-x-2 overflow-hidden py-1">
                            <?php while ($member = mysqli_fetch_assoc($members_res)): ?>
                                <div class="<?php echo $avatar_colors[array_rand($avatar_colors)]; ?> h-6 w-6 rounded-md ring-2 ring-white bg-slate-100 flex items-center justify-center text-[8px] font-bold uppercase text-slate-600">
                                    <?php echo substr($member['user_name'], 0, 1); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Refined Empty State for Group Management -->
            <div id="emptyGroups" class="col-span-full py-20 bg-white border-2 border-dashed border-slate-200 rounded-md text-center animate-fadeIn">
                <!-- Icon Circle -->
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-people-group text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-slate-600 font-bold text-lg">No Study Groups</h3>

                <!-- Description -->
                <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto px-6 leading-relaxed">
                    It looks like you haven't organized your users yet. Click the
                    <span class="text-indigo-600 font-bold">"Create New Group"</span>
                    button to start grouping members for assignments.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Group Creation Modal -->
<div id="groupModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 items-center justify-center p-4">
    <div class="bg-white rounded-md shadow-xl w-full max-w-md overflow-hidden animate-slideUp">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xl text-slate-800">Create <span id="nextGroupName" class="text-indigo-600"><?php echo $next_group_name; ?></span></h3>
                <p class="text-xs text-slate-400 font-medium">Select available members</p>
            </div>
            <button class="closeGroupModal text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>

        <div class="p-4 bg-white border-b border-slate-50">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="userSearchInput" placeholder="Search unassigned users..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-md outline-none focus:border-indigo-400 text-sm transition">
            </div>
        </div>

        <div class="max-h-64 overflow-y-auto p-4 space-y-2 hide-scrollbar" id="userSelectList">
            <?php if (mysqli_num_rows($available_users_query) > 0): ?>
                <?php while ($u = mysqli_fetch_assoc($available_users_query)):
                    // Assign random color to each user in the selection list
                    $randomColor = $avatar_colors[array_rand($avatar_colors)];
                ?>
                    <label class="user-select-item flex items-center justify-between p-3 rounded-md border border-slate-100 hover:bg-slate-50 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 <?php echo $randomColor; ?> rounded-md flex items-center justify-center text-xs font-bold">
                                <?php echo strtoupper(substr($u['user_name'], 0, 1)); ?>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 member-name"><?php echo $u['user_name']; ?></span>
                        </div>
                        <input type="checkbox" value="<?php echo $u['id']; ?>" class="member-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </label>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-10 text-slate-400 text-xs italic">
                    All users are currently assigned to groups.
                </div>
            <?php endif; ?>
        </div>

        <div class="p-6 bg-slate-50 flex gap-3">
            <button type="button" class="closeGroupModal flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-md font-bold hover:bg-slate-200 transition text-sm">Cancel</button>
            <button id="saveGroupBtn" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition text-sm">Confirm Group</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const nextGroup = "<?php echo $next_group_name; ?>";

        // Modal Controls
        $('#openGroupModal').on('click', function() {
            $('#groupModal').removeClass('hidden').addClass('flex');
        });

        $('.closeGroupModal').on('click', function() {
            $('#groupModal').addClass('hidden').removeClass('flex');
        });

        // Search logic
        $('#userSearchInput').on('keyup', function() {
            const query = $(this).val().toLowerCase();
            $('.user-select-item').each(function() {
                const name = $(this).find('.member-name').text().toLowerCase();
                $(this).toggle(name.includes(query));
            });
        });

        // Save Group
        $('#saveGroupBtn').off('click').on('click', function() {
            const selectedIds = [];
            $('.member-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                showToast("Selection Empty", "warning", "Please select at least one member.");
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: 'handlers/groups-action.php',
                type: 'POST',
                data: {
                    action: 'save',
                    group_name: nextGroup,
                    member_ids: selectedIds
                },
                success: function(res) {
                    if (res.status === 'success') {
                        showToast(res.title, 'success', res.description);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        showToast(res.title, 'error', res.description);
                        btn.prop('disabled', false).text('Confirm Group');
                    }
                }
            });
        });

        // Delete Group
        $(document).on('click', '.delete-group', function() {
            const card = $(this).closest('.group-card');
            const groupId = card.data('id');
            const gName = card.find('h3').text();

            showConfirmation("Delete Group?", `Delete ${gName}? Members will become available for new groups.`, function() {
                $.ajax({
                    url: 'handlers/groups-action.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        group_id: groupId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            card.fadeOut(300, function() {
                                $(this).remove();
                                if ($('.group-card').length === 0) location.reload();
                            });
                            showToast(res.title, 'success', res.description);
                        }
                    }
                });
            });
        });
    });
</script>