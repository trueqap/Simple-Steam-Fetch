jQuery(document).ready(function ($) {
    $('.select2-field').select2({
        placeholder: 'Please select an option',
        allowClear: true
    });

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
            processResults: function (data) {
                if (!data.success) {
                    alert(data.data.message);
                    return { results: [] };
                }
            
                const usedAppIds    = new Set();
                const uniqueResults = [];
            
                data.data.forEach(item => {
                    if (!usedAppIds.has(item.id)) {
                        usedAppIds.add(item.id);
                        uniqueResults.push(item);
                    }
                });
            
                return { results: uniqueResults };
            },
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
