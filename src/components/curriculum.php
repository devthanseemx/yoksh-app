<div class="mx-auto animate-fadeIn pb-20">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Curriculum Builder</h1>
            <p class="text-slate-500 mt-1">Manage your course structure in a uniform grid view.</p>
        </div>
        <button id="addNewModule" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-md font-semibold transition flex items-center gap-2 shadow-sm shadow-indigo-100">
            <i class="fas fa-plus-circle"></i> Add New Module
        </button>
    </div>

    <!-- Grid Container -->
    <div id="moduleGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start">
        <!-- Empty State -->
        <div id="emptyState" class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-md py-20 text-center">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-layer-group text-2xl"></i>
            </div>
            <h3 class="text-slate-600 font-medium">No modules created</h3>
            <p class="text-slate-400 text-sm">Create a module to start organizing chapters.</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    function updateChapterCount(moduleCard) {
        const count = moduleCard.find('.chapter-item').length;
        moduleCard.find('.chapter-count-badge').text(count + (count === 1 ? ' Chapter.' : ' Chapters.'));
    }

    // 1. Add New Module Card
    $('#addNewModule').off('click').on('click', function() {
        $('#emptyState').hide();
        
        const moduleHtml = `
            <div class="module-card bg-white border border-slate-200 rounded-md shadow-sm flex flex-col transition-all hover:border-indigo-300">
                <!-- Card Top Section (Fixed Header) -->
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-md flex items-center justify-center">
                            <i class="fas fa-folder"></i>
                        </div>
                        <button class="delete-module text-slate-300 hover:text-rose-500 p-2 transition">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                    
                    <!-- MODULE CODE WITH STATIC PREFIX EPU -->
                    <div class="flex items-center gap-0.5 mb-1">
                        <span class="text-indigo-500 font-bold text-[10px] tracking-widest uppercase">EPU</span>
                        <input type="text" placeholder="0000" maxlength="4"
                            class="bg-transparent font-bold text-indigo-500 text-[10px] uppercase tracking-widest outline-none w-full">
                    </div>

                    <!-- MODULE TITLE INPUT -->
                    <input type="text" placeholder="Module Title..." 
                        class="w-full bg-transparent font-bold text-slate-700 text-lg outline-none border-b-2 border-transparent focus:border-indigo-200 pb-1 mb-2">
                    
                    <div class="flex items-center justify-between mt-4">
                        <span class="chapter-count-badge text-[10px] font-black uppercase bg-slate-100 text-slate-500 px-2 py-1 rounded-md">0 Chapters.</span>
                        <button class="toggle-module-btn bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold py-1.5 px-3 rounded-md flex items-center gap-2 transition">
                            <span>Manage Content</span>
                            <i class="fas fa-chevron-down transition-transform duration-300"></i>
                        </button>
                    </div>
                </div>

                <!-- Card Bottom Section (Fixed Height Scrollable) -->
                <div class="module-body hidden border-t border-slate-50 bg-slate-50/50">
                    <div class="p-4 h-[400px] flex flex-col">
                        <button class="add-chapter-btn w-full py-2 bg-white border border-indigo-200 text-indigo-600 text-xs font-bold rounded-md hover:bg-indigo-50 transition mb-4 shrink-0 shadow-sm">
                            <i class="fas fa-plus mr-1"></i> New Chapter
                        </button>
                        <div class="chapter-list flex-1 overflow-y-auto hide-scrollbar space-y-3 pr-1"></div>
                    </div>
                </div>
            </div>
        `;
        
        $('#moduleGrid').append(moduleHtml);
    });

    // 2. Toggle Expansion
    $(document).off('click', '.toggle-module-btn').on('click', '.toggle-module-btn', function(e) {
        const card = $(this).closest('.module-card');
        const body = card.find('.module-body');
        const icon = $(this).find('i');
        body.slideToggle({
            duration: 300,
            start: function() {
                if(body.is(':hidden')) {
                    icon.addClass('rotate-180 text-indigo-500');
                    card.addClass('ring-2 ring-indigo-100 shadow-lg');
                } else {
                    icon.removeClass('rotate-180 text-indigo-500');
                    card.removeClass('ring-2 ring-indigo-100 shadow-lg');
                }
            }
        });
    });

    // 3. Add Chapter
    $(document).off('click', '.add-chapter-btn').on('click', '.add-chapter-btn', function(e) {
        const card = $(this).closest('.module-card');
        const chapterList = card.find('.chapter-list');
        const chapterHtml = `
            <div class="chapter-item bg-white border border-slate-200 rounded-md p-3 shadow-sm animate-fadeIn">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 flex-1">
                        <i class="fas fa-book text-emerald-500 text-[10px]"></i>
                        <input type="text" placeholder="Chapter title..." 
                            class="text-sm font-bold text-slate-700 bg-transparent outline-none w-full border-b border-transparent focus:border-emerald-200">
                    </div>
                    <button class="delete-chapter text-slate-300 hover:text-rose-500 ml-2">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="sub-chapter-list space-y-2 mb-2"></div>
                <button class="add-sub-btn text-[10px] font-bold text-emerald-500 hover:text-emerald-700 uppercase flex items-center gap-1">
                    <i class="fas fa-plus text-[8px]"></i> Sub-topic
                </button>
            </div>
        `;
        chapterList.append(chapterHtml);
        updateChapterCount(card);
        chapterList.animate({ scrollTop: chapterList.prop("scrollHeight") }, 500);
    });

    // 4. Add Sub-Chapter
    $(document).off('click', '.add-sub-btn').on('click', '.add-sub-btn', function(e) {
        const subList = $(this).closest('.chapter-item').find('.sub-chapter-list');
        const subHtml = `
            <div class="sub-chapter-item flex items-center justify-between gap-2 pl-3 border-l-2 border-slate-200 group animate-fadeIn">
                <input type="text" placeholder="Topic name..." 
                    class="text-xs text-slate-500 bg-transparent outline-none w-full py-1 focus:text-slate-800">
                <button class="delete-sub opacity-0 group-hover:opacity-100 text-slate-300 hover:text-rose-500 transition">
                    <i class="fas fa-trash text-[9px]"></i>
                </button>
            </div>
        `;
        subList.append(subHtml);
    });

    // 5. Delete Logic
    $(document).off('click', '.delete-module').on('click', '.delete-module', function() {
        if(confirm('Remove this module and all its contents?')) {
            $(this).closest('.module-card').remove();
            if($('.module-card').length === 0) $('#emptyState').show();
        }
    });

    $(document).on('click', '.delete-chapter', function() {
        const card = $(this).closest('.module-card');
        $(this).closest('.chapter-item').remove();
        updateChapterCount(card);
    });

    $(document).on('click', '.delete-sub', function() {
        $(this).closest('.sub-chapter-item').remove();
    });
});
</script>