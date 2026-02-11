<?php
include '../../config/db.php';
$modules_query = mysqli_query($conn, "SELECT * FROM modules ORDER BY id ASC");
?>

<div class="mx-auto animate-fadeIn pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Learning Progress</h1>
            <p class="text-slate-500 mt-1">Check off topics as you complete them to track your journey.</p>
        </div>
        
        <!-- Global Progress Stats -->
        <div class="flex items-center gap-6 bg-white p-4 rounded-md border border-slate-200 shadow-sm">
            <div class="text-center px-4 border-r border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Overall</p>
                <p id="total-progress-percent" class="text-xl font-black text-emerald-500">0%</p>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between text-[10px] font-bold text-slate-500 uppercase">
                    <span>Course Completion</span>
                    <span id="stat-completed-count">0/0</span>
                </div>
                <div class="w-48 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="total-progress-bar" class="bg-emerald-500 h-full transition-all duration-700" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Container -->
    <div id="progress-accordion" class="space-y-4">
        
        <?php if (mysqli_num_rows($modules_query) > 0): ?>
            <?php while ($mod = mysqli_fetch_assoc($modules_query)): 
                $m_id = $mod['id'];
                $chapters_query = mysqli_query($conn, "SELECT * FROM chapters WHERE module_id = '$m_id'");
            ?>
            <!-- Module Card -->
            <div class="module-wrapper bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
                <div class="module-header p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition select-none">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-md flex items-center justify-center">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-700 module-title-text"><?php echo $mod['module_title']; ?></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">EPU-<?php echo $mod['module_code']; ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="hidden md:block text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Module Progress</p>
                            <p class="module-perc-text text-sm font-bold text-slate-600">0%</p>
                        </div>
                        <i class="fas fa-chevron-down text-slate-300 transition-transform duration-300 module-chevron"></i>
                    </div>
                </div>

                <div class="module-content hidden border-t border-slate-50">
                    <div class="p-4 space-y-3 bg-slate-50/30">
                        
                        <?php while ($chap = mysqli_fetch_assoc($chapters_query)): 
                            $c_id = $chap['id'];
                            $subs_query = mysqli_query($conn, "SELECT * FROM sub_chapters WHERE chapter_id = '$c_id'");
                        ?>
                        <!-- Chapter Item -->
                        <div class="chapter-card bg-white border border-slate-100 rounded-md overflow-hidden">
                            <div class="chapter-header p-3 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-book text-slate-300 text-xs"></i>
                                    <span class="text-sm font-semibold text-slate-600"><?php echo $chap['chapter_title']; ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="chapter-stat text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded">0/0 Completed</span>
                                    <i class="fas fa-plus text-[10px] text-slate-300 chapter-plus-icon"></i>
                                </div>
                            </div>
                            
                            <div class="chapter-body hidden p-4 border-t border-slate-50">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <?php while ($sub = mysqli_fetch_assoc($subs_query)): ?>
                                    <label class="sub-item flex items-center gap-3 p-3 rounded-md border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/30 cursor-pointer transition">
                                        <input type="checkbox" class="topic-check" 
                                               data-id="<?php echo $sub['id']; ?>" 
                                               <?php echo ($sub['is_completed'] == 1) ? 'checked' : ''; ?>>
                                        <span class="text-xs text-slate-600 font-medium"><?php echo $sub['sub_title']; ?></span>
                                    </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>

                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="p-20 text-center bg-white border border-dashed border-slate-200 rounded-md">
                <p class="text-slate-400">No curriculum found. Please build your curriculum first.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
$(document).ready(function() {

    //Module Toggle Logic (No change here)
    $(document).off('click', '.module-header').on('click', '.module-header', function() {
        const wrapper = $(this).closest('.module-wrapper');
        const content = wrapper.find('.module-content');
        const chevron = $(this).find('.module-chevron');
        content.slideToggle(300);
        chevron.toggleClass('rotate-180 text-indigo-500');
    });

    // Chapter Toggle Logic (No change here)
    $(document).off('click', '.chapter-header').on('click', '.chapter-header', function() {
        const body = $(this).next('.chapter-body');
        const icon = $(this).find('.chapter-plus-icon');
        body.slideToggle(200);
        icon.toggleClass('fa-plus fa-minus text-emerald-500');
    });

    //  ENHANCED Calculation Logic (Now faster)
    function calculateAll() {
        const allChecks = $('.topic-check');
        const checkedOnes = $('.topic-check:checked');
        
        // Calculate Overall Stats
        const totalCount = allChecks.length;
        const completedCount = checkedOnes.length;
        const totalPercent = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;

        // Update Global UI Immediately
        $('#total-progress-bar').css('width', totalPercent + '%');
        $('#total-progress-percent').text(totalPercent + '%');
        $('#stat-completed-count').text(completedCount + '/' + totalCount);

        // Update each Module Card
        $('.module-wrapper').each(function() {
            const mChecks = $(this).find('.topic-check');
            const mChecked = $(this).find('.topic-check:checked');
            const mPercent = mChecks.length > 0 ? Math.round((mChecked.length / mChecks.length) * 100) : 0;
            
            $(this).find('.module-perc-text').text(mPercent + '%');

            // Update each Chapter inside the Module
            $(this).find('.chapter-card').each(function() {
                const cChecks = $(this).find('.topic-check');
                const cChecked = $(this).find('.topic-check:checked');
                
                $(this).find('.chapter-stat').text(cChecked.length + '/' + cChecks.length + ' Completed');
                
                // Visual feedback for completed chapters
                if(cChecks.length > 0 && cChecked.length === cChecks.length) {
                    $(this).find('.chapter-header').addClass('bg-emerald-50/50');
                    $(this).find('.fa-book').addClass('text-emerald-500');
                } else {
                    $(this).find('.chapter-header').removeClass('bg-emerald-50/50');
                    $(this).find('.fa-book').removeClass('text-emerald-500');
                }
            });
        });
    }

    // IMMEDIATE ACTION on Change
    $(document).off('change', '.topic-check').on('change', '.topic-check', function() {
        calculateAll();

        const subId = $(this).data('id');
        const status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'handlers/progress-action.php',
            type: 'POST',
            data: { sub_id: subId, status: status },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    showToast("Progress Updated", "success", res.message);
                } else {
                    showToast("Error", "error", res.message);
                    location.reload(); 
                }
            },
            error: function(xhr, status, error) {
                if(status !== 'parsererror') {
                    showToast("Connection Error", "error", "Could not reach the server.");
                }
            }
        });
    });

    calculateAll();
});
</script>