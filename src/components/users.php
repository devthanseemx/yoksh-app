<?php
include '../../config/db.php';
$users_query = mysqli_query($conn, "SELECT *, DATE_FORMAT(created_at, '%b %d, %Y') as date_added FROM users ORDER BY id DESC");
?>

<div class="max-w-full mx-auto animate-fadeIn pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">User Management</h1>
            <p class="text-slate-500 mt-1">Manage and organize team members or students.</p>
        </div>
        <button id="openUserModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-md font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-100">
            <i class="fas fa-user-plus"></i> Add New User
        </button>
    </div>

    <!-- Users Grid -->
    <div id="userGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
        
        <?php 
        $colors = ['bg-indigo-100 text-indigo-600', 'bg-emerald-100 text-emerald-600', 'bg-rose-100 text-rose-600', 'bg-sky-100 text-sky-600', 'bg-amber-100 text-amber-600'];
        
        if (mysqli_num_rows($users_query) > 0):
            while ($row = mysqli_fetch_assoc($users_query)): 
                $initial = strtoupper(substr($row['user_name'], 0, 1));
                $randomColor = $colors[array_rand($colors)];
        ?>
            <!-- User Card -->
            <div class="user-card bg-white border border-slate-200 rounded-md p-4 shadow-sm hover:shadow-md transition-all flex flex-row gap-4 h-full relative" data-id="<?php echo $row['id']; ?>">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 <?php echo $randomColor; ?> rounded-md flex items-center justify-center font-bold text-xl uppercase">
                        <?php echo $initial; ?>
                    </div>
                </div>

                <div class="flex flex-col flex-1 min-w-0">
                    <h3 class="font-bold text-slate-800 truncate user-name text-md leading-tight mb-1.5"><?php echo $row['user_name']; ?></h3>
                    
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="fas fa-phone-alt text-[9px] w-3 text-indigo-400"></i>
                            <span class="text-xs font-medium user-phone truncate"><?php echo $row['user_phone']; ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="fas fa-calendar-alt text-[9px] w-3 text-emerald-400"></i>
                            <span class="text-[10px] font-medium user-date truncate">Added: <?php echo $row['date_added']; ?></span>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end items-center gap-3">
                        <button class="edit-user text-slate-400 hover:text-indigo-600 transition" title="Edit">
                            <i class="fas fa-pen text-[10px]"></i>
                        </button>
                        <button class="delete-user text-slate-400 hover:text-rose-500 transition" title="Delete">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden z-50 items-center justify-center p-4">
    <div class="bg-white rounded-md shadow-xl w-full max-w-sm animate-fadeIn">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800" id="modalTitle">Add New User</h3>
            <button class="closeModal text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="userForm" class="p-5 space-y-4">
            <input type="hidden" id="userIdInput">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Full Name</label>
                <input type="text" id="userNameInput" placeholder="Enter name" class="w-full bg-slate-50 border border-slate-200 rounded-md px-4 py-2 outline-none focus:border-indigo-500 transition text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Phone Number</label>
                <input type="tel" id="userPhoneInput" placeholder="+94 7X XXX XXXX" class="w-full bg-slate-50 border border-slate-200 rounded-md px-4 py-2 outline-none focus:border-indigo-500 transition text-sm">
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" class="closeModal flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-md font-bold hover:bg-slate-50 transition text-xs">Cancel</button>
                <button type="submit" id="submitUserBtn" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition text-xs">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Open Modal for Add
    $('#openUserModal').off('click').on('click', function() {
        $('#userIdInput').val('');
        $('#modalTitle').text('Add New User');
        $('#userForm')[0].reset();
        $('#userModal').removeClass('hidden').addClass('flex');
    });

    // Close Modal
    $('.closeModal').on('click', function() {
        $('#userModal').addClass('hidden').removeClass('flex');
    });

    // Save/Update AJAX
    $('#userForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            user_id: $('#userIdInput').val(),
            user_name: $('#userNameInput').val(),
            user_phone: $('#userPhoneInput').val(),
            action: 'save'
        };

        const btn = $('#submitUserBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'handlers/users-action.php',
            type: 'POST',
            data: data,
            success: function(res) {
                if(res.status === 'success') {
                    showToast(res.title, 'success', res.description);
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(res.title, 'error', res.description);
                    btn.prop('disabled', false).text('Save User');
                }
            }
        });
    });

    // Edit Click
    $(document).on('click', '.edit-user', function() {
        const card = $(this).closest('.user-card');
        $('#userIdInput').val(card.data('id'));
        $('#userNameInput').val(card.find('.user-name').text());
        $('#userPhoneInput').val(card.find('.user-phone').text());
        $('#modalTitle').text('Edit User');
        $('#userModal').removeClass('hidden').addClass('flex');
    });

    // Delete Click
    $(document).on('click', '.delete-user', function() {
        const card = $(this).closest('.user-card');
        const userId = card.data('id');
        const name = card.find('.user-name').text();

        showConfirmation("Delete User?", `Are you sure you want to remove ${name}?`, function() {
            $.ajax({
                url: 'handlers/users-action.php',
                type: 'POST',
                data: { action: 'delete', id: userId },
                success: function(res) {
                    if(res.status === 'success') {
                        card.fadeOut(300, function() { $(this).remove(); });
                        showToast(res.title, 'success', res.description);
                    }
                }
            });
        });
    });
});
</script>