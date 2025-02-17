jQuery(document).ready(function ($) {
    $('.select2-field').select2({
        placeholder: 'Write something',
        allowClear: true
    });

    function process_steam_results(data) {
        if (!data.success) {
            alert(data.data.message);
            return { results: [] };
        }
    
        const used_app_ids = new Set();
        const unique_results = [];
    
        data.data.forEach(function(item) {
            if (!used_app_ids.has(item.id)) {
                used_app_ids.add(item.id);
                unique_results.push(item);
            }
        });
    
        return { results: unique_results };
    }

    $('#steam-game-search').select2({
        placeholder: 'Search for a Steam game',
        minimumInputLength: 3,
        allowClear: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'hw_search_steam_games',
                    query: params.term
                };
            },
            processResults: process_steam_results,
            cache: false
        }
    });

    // Auto-fill App ID when a game is selected
    $('#steam-game-search').on('select2:select', function (e) {
        var selectedData = e.params.data;
        $('#steam-app-id').val(selectedData.id);
    });

    function toggleDetailedDescriptionMeta() {
        if ($('#hw_steam_save_description').is(':checked')) {
            $('#hw_steam_detailed_description_meta').closest('.hw-form-group').hide();
        } else {
            $('#hw_steam_detailed_description_meta').closest('.hw-form-group').show();
        }
    }

    // Initial check on page load
    toggleDetailedDescriptionMeta();

    // Handle checkbox change event
    $('#hw_steam_save_description').on('change', function () {
        toggleDetailedDescriptionMeta();
    });

});
