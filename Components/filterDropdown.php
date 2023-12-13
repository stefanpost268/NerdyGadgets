<div id="accordion-color" class="md:hidden m-1 mt-5" data-accordion="close" data-active-classes="">
    <h2 id="accordion-color-heading-1">
        <button type="button" class="flex items-center justify-between w-full p-3 font-medium rtl:text-right border border-b-0 rounded-t-xl border-gray-700 text-gray-400 bg-gray-800 gap-3" data-accordion-target="#accordion-color-body-1" aria-expanded="false" aria-controls="accordion-color-body-1">
            <span>
                Filteren
            </span>
            <svg data-accordion-icon class="w-3 h-3 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
            </svg>
        </button>
    </h2>
    <div id="accordion-color-body-1" class="hidden" aria-labelledby="accordion-color-heading-1">
        <div class="p-5 border border-b-0 border-gray-700 bg-gray-900 rounded">
            <?php include "./Components/filterFormData.php" ?>
        </div>
    </div>
</div>
