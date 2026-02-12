<?php
include '../../config/db.php';
?>

<style>
    @font-face {
        font-family: 'MyLocalTamil';
        src: url('../assets/fonts/Baamini.ttf');
    }

    .tamil-text-input,
    .mod-title,
    .chapter-title-display,
    .sub-title-display {
        font-family: 'MyLocalTamil', sans-serif !important;
        line-height: 1.6 !important;
    }

    input::placeholder,
    button,
    .chapter-count-badge,
    span:not([class*="-display"]),
    .uppercase {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        line-height: normal !important;
    }
</style>

<div class="mx-auto animate-fadeIn pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 bg-white p-6 rounded-md border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Curriculum Builder</h1>
            <p class="text-slate-500 mt-1">Manage course modules and chapters.</p>
        </div>
        <button id="addNewModule" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-md font-semibold transition flex items-center gap-2 shadow-sm shadow-indigo-100">
            <i class="fas fa-plus-circle"></i> Add New Module
        </button>
    </div>

    <!-- Grid Container -->
    <div id="moduleGrid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8 items-start">

        <?php
        $modules_query = mysqli_query($conn, "SELECT * FROM modules ORDER BY id DESC");

        if (mysqli_num_rows($modules_query) > 0) {
            while ($mod = mysqli_fetch_assoc($modules_query)) {
                $m_id = $mod['id'];
                $chap_res = mysqli_query($conn, "SELECT * FROM chapters WHERE module_id = $m_id");
                $chap_count = mysqli_num_rows($chap_res);
        ?>
                <!-- SAVED MODULE CARD -->
                <div class="module-card bg-white border border-slate-200 rounded-md shadow-sm flex flex-col transition-all hover:border-indigo-300" data-db-id="<?php echo $m_id; ?>">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-md flex items-center justify-center">
                                <i class="fas fa-folder"></i>
                            </div>
                            <button class="delete-module text-slate-300 hover:text-rose-500 p-2 transition">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>

                        <div class="flex items-center gap-0.5 mb-1">
                            <span class="text-indigo-500 font-bold text-[10px] tracking-widest uppercase">EPU</span>
                            <input type="text" value="<?php echo $mod['module_code']; ?>" disabled class="mod-code bg-transparent font-bold text-indigo-500 text-[10px] uppercase tracking-widest outline-none w-full">
                        </div>

                        <!-- DATA INPUT (Tamil Font Applied) -->
                        <input type="text" value="<?php echo $mod['module_title']; ?>" disabled class="mod-title w-full bg-transparent font-bold text-slate-700 text-lg outline-none border-b-2 border-transparent focus:border-indigo-200 pb-1 mb-2">

                        <div class="flex items-center justify-between mt-4">
                            <span class="chapter-count-badge text-[10px] font-black uppercase bg-slate-100 text-slate-500 px-2 py-1 rounded-md"><?php echo $chap_count; ?> Chapters.</span>
                            <button class="toggle-module-btn bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold py-1.5 px-3 rounded-md flex items-center gap-2 transition">
                                <span>Manage Content</span>
                                <i class="fas fa-chevron-down transition-transform duration-300"></i>
                            </button>
                        </div>
                    </div>

                    <div class="module-body hidden border-t border-slate-50 bg-slate-50/50">
                        <div class="p-4 max-h-[300px] flex flex-col">
                            <div class="chapter-list flex-1 overflow-y-auto space-y-3 pr-1">
                                <?php
                                while ($chap = mysqli_fetch_assoc($chap_res)) {
                                    $c_id = $chap['id'];
                                    $sub_res = mysqli_query($conn, "SELECT * FROM sub_chapters WHERE chapter_id = $c_id");
                                ?>
                                    <div class="chapter-item bg-white border border-slate-200 rounded-md p-3 shadow-sm">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i class="fas fa-book text-emerald-500 text-[10px]"></i>
                                            <!-- DATA DISPLAY (Tamil Font Applied via Class) -->
                                            <span class="chapter-title-display text-sm font-bold text-slate-700"><?php echo $chap['chapter_title']; ?></span>
                                        </div>
                                        <div class="sub-chapter-list space-y-1.5">
                                            <?php while ($sub = mysqli_fetch_assoc($sub_res)) { ?>
                                                <!-- DATA DISPLAY (Tamil Font Applied via Class) -->
                                                <div class="sub-title-display flex items-center gap-2 pl-3 border-l-2 border-slate-200 text-xs text-slate-500">
                                                    <?php echo $sub['sub_title']; ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else { ?>
            <div id="emptyState" class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-md py-20 text-center animate-fadeIn">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
                <h3 class="text-slate-600 font-bold text-lg">No Curriculum Modules</h3>
                <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto px-6 leading-relaxed">
                    Your curriculum is currently empty. Click the <b>"Add New Module"</b> button above to start organizing your chapters and sub-topics.
                </p>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function() {

        function updateChapterCount(moduleCard) {
            const count = moduleCard.find('.chapter-item').length;
            moduleCard.find('.chapter-count-badge').text(count + (count === 1 ? ' Chapter.' : ' Chapters.'));
        }

        // Add New Module (Added tamil-text-input only to the Title)
        $('#addNewModule').off('click').on('click', function() {
            $('#emptyState').hide();
            const moduleHtml = `
            <div class="module-card bg-white border border-slate-200 rounded-md shadow-sm flex flex-col transition-all hover:border-indigo-300 animate-slideUp">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-md flex items-center justify-center"><i class="fas fa-folder"></i></div>
                        <div class="flex gap-2">
                            <button class="save-btn text-slate-300 hover:text-emerald-600 p-2 transition"><i class="fas fa-save text-sm"></i></button>
                            <button class="delete-module text-slate-300 hover:text-rose-500 p-2 transition"><i class="fas fa-trash-alt text-sm"></i></button>
                        </div>
                    </div>
                    <div class="flex items-center gap-0.5 mb-1">
                        <span class="text-indigo-500 font-bold text-[10px] tracking-widest uppercase">EPU</span>
                        <input type="text" placeholder="0000" maxlength="4" class="mod-code bg-transparent font-bold text-indigo-500 text-[10px] uppercase tracking-widest outline-none w-full">
                    </div>
                    <!-- ONLY THIS INPUT GETS TAMIL FONT -->
                    <input type="text" placeholder="Module Title..." class="mod-title tamil-text-input w-full bg-transparent font-bold text-slate-700 text-lg outline-none border-b-2 border-transparent focus:border-indigo-200 pb-1 mb-2">
                    
                    <div class="flex items-center justify-between mt-4">
                        <span class="chapter-count-badge text-[10px] font-black uppercase bg-slate-100 text-slate-500 px-2 py-1 rounded-md">0 Chapters.</span>
                        <button class="toggle-module-btn bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold py-1.5 px-3 rounded-md flex items-center gap-2 transition">
                            <span>Manage Content</span> <i class="fas fa-chevron-down transition-transform"></i>
                        </button>
                    </div>
                </div>
                <div class="module-body hidden border-t border-slate-50 bg-slate-50/50">
                    <div class="p-4 max-h-[320px] flex flex-col">
                        <button class="add-chapter-btn w-full py-2 bg-white border border-indigo-200 text-indigo-600 text-xs font-bold rounded-md hover:bg-indigo-50 transition mb-4 shrink-0 shadow-sm">
                            <i class="fas fa-plus mr-1"></i> New Chapter
                        </button>
                        <div class="chapter-list flex-1 overflow-y-auto space-y-3 pr-1"></div>
                    </div>
                </div>
            </div>`;
            $('#moduleGrid').prepend(moduleHtml);
        });

        // Add Chapter (Added tamil-text-input class)
        $(document).off('click', '.add-chapter-btn').on('click', '.add-chapter-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const chapterHtml = `
            <div class="chapter-item bg-white border border-slate-200 rounded-md p-3 shadow-sm mb-3">
                <div class="flex items-center justify-between mb-2">
                    <input type="text" placeholder="Chapter title..." class="ch-title tamil-text-input text-sm font-bold text-slate-700 bg-transparent outline-none w-full border-b border-transparent focus:border-emerald-200">
                    <button class="delete-chapter text-slate-300 hover:text-rose-500 ml-2"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div class="sub-chapter-list space-y-2 mb-2"></div>
                <button class="add-sub-btn text-[10px] font-bold text-emerald-500 uppercase flex items-center gap-1"><i class="fas fa-plus text-[8px]"></i> Sub-topic</button>
            </div>`;
            $(this).closest('.module-body').find('.chapter-list').append(chapterHtml);
            updateChapterCount($(this).closest('.module-card'));
        });

        // Add Sub-Chapter (Added tamil-text-input class)
        $(document).off('click', '.add-sub-btn').on('click', '.add-sub-btn', function(e) {
            e.preventDefault();
            const subHtml = `
            <div class="sub-chapter-item flex items-center justify-between gap-2 pl-3 border-l-2 border-slate-200">
                <input type="text" placeholder="Topic name..." class="sub-title tamil-text-input text-xs text-slate-500 bg-transparent outline-none w-full py-1">
                <button class="delete-sub text-slate-300 hover:text-rose-500"><i class="fas fa-trash text-[9px]"></i></button>
            </div>`;
            $(this).closest('.chapter-item').find('.sub-chapter-list').append(subHtml);
        });

        $(document).off('click', '.toggle-module-btn').on('click', '.toggle-module-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const body = $(this).closest('.module-card').find('.module-body');
            $('.module-body').not(body).slideUp(200);
            body.slideToggle(250);
            $(this).find('i').toggleClass('rotate-180');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.module-card').length) {
                $('.module-body').slideUp(200);
                $('.toggle-module-btn i').removeClass('rotate-180');
            }
        });

        $(document).on('click', '.delete-module', function(e) {
            e.stopPropagation();
            const card = $(this).closest('.module-card');
            const dbId = card.attr('data-db-id');
            if (!dbId) {
                card.remove();
                return;
            }

            showConfirmation("Delete Module?", "This will remove all chapters and sub-topics.", function() {
                $.ajax({
                    url: 'handlers/curriculum-action.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        module_id: dbId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            card.fadeOut(300, function() {
                                $(this).remove();
                            });
                            showToast(res.title, 'success', res.description);
                        }
                    }
                });
            });
        });


        $(document).off('click.saveModule').on('click.saveModule', '.save-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const card = $(this).closest('.module-card');
            const btn = $(this);
            if (btn.hasClass('is-saving')) return;

            const data = {
                action: 'save',
                module_code: card.find('.mod-code').val(),
                module_title: card.find('.mod-title').val(),
                chapters: []
            };

            if (!data.module_code || !data.module_title) {
                showToast("Error", "error", "Enter Module Code and Title.");
                return;
            }

            card.find('.chapter-item').each(function() {
                const ch = {
                    title: $(this).find('.ch-title').val(),
                    subs: []
                };
                $(this).find('.sub-title').each(function() {
                    if ($(this).val().trim() !== "") ch.subs.push($(this).val());
                });
                if (ch.title.trim() !== "") data.chapters.push(ch);
            });

            btn.addClass('is-saving').html('<i class="fas fa-spinner fa-spin text-sm"></i>');

            $.ajax({
                url: 'handlers/curriculum-action.php',
                type: 'POST',
                data: data,
                success: function(res) {
                    if (res.status === 'success') {
                        showToast(res.title, 'success', res.description);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(res.title, 'error', res.description);
                        btn.removeClass('is-saving').html('<i class="fas fa-save text-sm"></i>');
                    }
                }
            });
        });

        $(document).on('click', '.delete-module', function(e) {
            e.stopPropagation();
            const card = $(this).closest('.module-card');
            const dbId = card.attr('data-db-id');
            if (!dbId) {
                card.remove();
                return;
            }
            showConfirmation("Delete Module?", "This will remove all chapters and sub-topics.", function() {
                $.ajax({
                    url: 'handlers/curriculum-action.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        module_id: dbId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            card.fadeOut(300, function() {
                                $(this).remove();
                            });
                            showToast(res.title, 'success', res.description);
                        }
                    }
                });
            });
        });

        $(document).on('click', '.delete-chapter', function() {
            $(this).closest('.chapter-item').remove();
            updateChapterCount($(this).closest('.module-card'));
        });
        $(document).on('click', '.delete-sub', function() {
            $(this).closest('.sub-chapter-item').remove();
        });
    });
</script>